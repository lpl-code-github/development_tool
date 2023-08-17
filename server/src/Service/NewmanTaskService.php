<?php

namespace App\Service;

use App\Factory\ExceptionFactory;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\NewmanTask;
use App\Dto\NewmanTaskDto;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\Process\Process;

class NewmanTaskService
{
    private EntityManagerInterface $entityManager;
    private CacheInterface $customThingCache;

    public function __construct(
        EntityManagerInterface $entityManager,
        CacheInterface         $customThingCache
    )
    {
        $this->entityManager = $entityManager;
        $this->customThingCache = $customThingCache;
    }

    /**
     * @param array $params
     * @param array $returnFields
     * @return array
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Exception
     */
    public function handleGetNewmanTasks(array $params, array $returnFields): array
    {
        $result = array();

        /**
         * 查询全部
         */
        if (count($params) == 0){
            $newmanTasks = $this->entityManager->getRepository(NewmanTask::class)->findAllTasks();
            foreach ($newmanTasks as $newmanTask){
                $newmanTaskDto = new NewmanTaskDto($newmanTask);
                $result[] = $newmanTaskDto->toArray($returnFields);
            }
            return $result;
        }
        /**
         * 模糊匹配
         */
        if (array_key_exists('key',$params)){
            $newmanTasks = $this->entityManager->getRepository(NewmanTask::class)->findLikeNameOrDesc($params['key']);
            foreach ($newmanTasks as $newmanTask){
                $newmanTaskDto = new NewmanTaskDto($newmanTask);
                $result[] = $newmanTaskDto->toArray($returnFields);
            }
            return $result;
        }
        /**
         * 查询未完成的测试任务，也就是正在完成的任务（active = 0），
         * 由于handlePostNewmanTasks只能创建一个active = 0的任务，所以只会有一个测试任务在进行
         *
         *      1.查看缓存中是否存在newman的进程，
         *        如果有，获取缓存的 newman命令输出的报告地址，和newman命令的进程id
         *          2.通过ps命令看newman的进程是否还在执行中
         *            如果不在执行，说明newman已经完成，通过newman命令的cli输出去查看newman是否执行成功
         *                3.更新状态,newman是否执行成功作为日志最后一步的状态，也作为整个测试任务的状态
         *
         */
        if (array_key_exists("is_unfinished", $params)) {
            if ($params["is_unfinished"]) {
                $newmanTask = $this->entityManager->getRepository(NewmanTask::class)->findUnfinishedTasks();

                if ($newmanTask) {
                    /**@var NewmanTask $newmanTask */
                    $newmanTaskDto = new NewmanTaskDto($newmanTask);
                    $result[] = $newmanTaskDto->toArray($returnFields);
                    $taskId = $newmanTask->getId();

                    // 检查缓存中是否存在task
                    $processItem = $this->customThingCache->getItem($taskId . "_newman_process");

                    if ($processItem->isHit()) {
                        // 如果缓存命中，获取缓存的 newman的一些参数以及进程id
                        $processInfo = $processItem->get();
                        $htmlReportPath = $processInfo['html_report_path'];
                        $excelReportPath = $processInfo['excel_report_path'];
                        $cliOutputPath = $processInfo['cli_output_path'];

                        $processRunningStatus = $this->getProcessRunningStatus($processInfo['pid']);

                        if (!$processRunningStatus) { // 如果newman进程不再运行了
                            // 判断cli_output文件第一行是否包含"newman"字符串且不包含"error"字符串,
                            try {
                                // 文件内容可能还没有被写进去，所以这里20秒内去获取文件内容
                                $maxWaitTime = 20;
                                $startTime = time();
                                $newmanTaskStatus  = "不确定";
                                $newmanRunStatus  = "不确定";
                                while (true) {
                                    if (file_exists($cliOutputPath)) {
                                        // 读取文件内容
                                        $firstLine = fgets(fopen($cliOutputPath, 'r'));
                                        if (!empty($firstLine) && $firstLine!='') {// 文件内容不为空
                                            $flag = (strpos($firstLine, 'newman') !== false && strpos($firstLine, 'error') == false && strpos($firstLine, 'failed') == false);
                                            $newmanTaskStatus = $flag ? "成功":'失败';
                                            $newmanRunStatus = $flag ?"成功":'失败';
                                            break; // 退出循环
                                        }
                                    }
                                    // 判断是否超过最大等待时间
                                    $currentTime = time();
                                    if ($currentTime - $startTime >= $maxWaitTime) {
                                        break;
                                    }
                                    // 等待一段时间再重新尝试读取文件
                                    sleep(1);
                                }
                            } catch (\Exception $e) {
                                throw ExceptionFactory::InternalServerException("cli_output文件未找到");
                            }

                            $newmanTask->setStatus($newmanTaskStatus);// 设置任务状态
                            $log = $newmanTask->getLog();
                            foreach ($log as &$item) {
                                if ($item['index'] == 4) {
                                    $item['flag'] = true;
                                    $item['status'] = $newmanRunStatus;
                                }
                            }
                            $newmanTask->setLog($log);
                            $newmanTask->setHtmlReportPath($htmlReportPath);
                            $newmanTask->setExcelReportPath($excelReportPath);
                            $newmanTask->setCliOutputPath($cliOutputPath);

                            $newmanTask->setActive(1);
                            $this->entityManager->persist($newmanTask);
                            $this->entityManager->flush();

                            // 任务设置结束才删除缓存
                            $this->customThingCache->delete($taskId . "_newman_process");
                        }
                    }
                }
            }
        }

        return $result;
    }

    /**
     * 执行一个shell脚本
     * 判断一个进程是否存在（是否在运行）
     * @param $pid
     * @return bool
     */
    private function getProcessRunningStatus($pid): bool
    {
        $command = 'if ps -p ' . $pid . ' -o pid= > /dev/null; then echo -n true; else echo -n false; fi';
        $process = Process::fromShellCommandline($command);
        $process->run();
        return $process->getOutput() == "true";
    }

    /**
     * @param NewmanTask $newmanTask
     * @param array $returnFields
     * @return array
     * @throws \Exception
     */
    public function handlePostNewmanTasks(NewmanTask $newmanTask, array $returnFields): array
    {
        $tempNewmanTask = $this->entityManager->getRepository(NewmanTask::class)->findUnfinishedTasks();
        if ($tempNewmanTask) {
            throw ExceptionFactory::WrongFormatException("只允许创建一个待执行的任务");
        }

        $this->entityManager->persist($newmanTask);
        $this->entityManager->flush();

        $newmanTaskDto = new NewmanTaskDto($newmanTask);
        $result[] = $newmanTaskDto->toArray($returnFields);
        return $result;
    }

    /**
     * @param array $params
     * @param array $returnFields
     * @return array
     * @throws \Exception
     */
    public function handlePutNewmanTasks(array $params, array $returnFields): array
    {
        /**
         * @var NewmanTask $newmanTask
         */
        $newmanTask = $this->entityManager->getRepository(NewmanTask::class)->findOneById($params['id']);
        if (!$newmanTask){
            throw ExceptionFactory::NotFoundException("未找到");
        }

        if (array_key_exists('log', $params)) {
            $newmanTask->setLog($params["log"]);
        }
        if (array_key_exists('status', $params)) {
            if ($params["status"] == 'error'){
                $newmanTask->setActive(1);
            }
            $newmanTask->setStatus($params["status"]);
        }
        if (array_key_exists('name', $params)) {
            $newmanTask->setName($params["name"]);
        }

        if (array_key_exists('description', $params)) {
            $newmanTask->setDescription($params["description"]);
        }

        $this->entityManager->persist($newmanTask);
        $this->entityManager->flush();


        $newmanTaskDto = new NewmanTaskDto($newmanTask);
        $result[] = $newmanTaskDto->toArray($returnFields);
        return $result;
    }

    /**
     * @param array $params
     * @param array $returnFields
     * @return array
     * @throws \Exception
     */
    public function handleDeleteNewmanTasks(array $params, array $returnFields): array
    {
        $result = [];

        // example
        if (array_key_exists('id', $params)) {
            $newmanTask = $this->entityManager->getRepository(NewmanTask::class)->findOneById($params['id']);
            $result[] = $this->deleteNewmanTask($newmanTask, $returnFields);
        }

        if (array_key_exists('ids', $params)) {
            $newmanTasks = $this->entityManager->getRepository(NewmanTask::class)->findByIds($params['ids']);
            foreach ($newmanTasks as $newmanTask) {
                $result[] = $this->deleteNewmanTask($newmanTask, $returnFields);
            }
        }

        return $result;
    }

    /**
     * @param NewmanTask $newmanTask
     * @param array $returnFields
     * @return array
     */
    private function deleteNewmanTask(NewmanTask $newmanTask, array $returnFields): array
    {
        $this->entityManager->beginTransaction();
        try {
            $filesystem = new Filesystem();
            $cliOutputPath = $newmanTask->getCliOutputPath();
            $excelReportPath = $newmanTask->getExcelReportPath();
            $htmlReportPath = $newmanTask->getHtmlReportPath();

            $newmanTaskDto = new NewmanTaskDto($newmanTask);
            $this->entityManager->getRepository(NewmanTask::class)->remove($newmanTask, true);

            $filesystem->remove($cliOutputPath);
            $filesystem->remove($excelReportPath);
            $filesystem->remove($htmlReportPath);

            $this->entityManager->commit();

            return $newmanTaskDto->toArray($returnFields);
        }catch (\Exception $exception){
            $this->entityManager->rollback();
            throw ExceptionFactory::InternalServerException("删除失败：".$exception->getMessage());
        }
    }
}