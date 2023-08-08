<?php

namespace App\Service;

use App\Factory\ExceptionFactory;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\NewmanTask;
use App\Dto\NewmanTaskDto;
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
                                $newmanTaskStatus  = "not sure";
                                $newmanRunStatus  = "not sure";
                                while (true) {
                                    if (file_exists($cliOutputPath)) {
                                        // 读取文件内容
                                        $firstLine = fgets(fopen($cliOutputPath, 'r'));
                                        if (!empty($firstLine) && $firstLine!='') {// 文件内容不为空
                                            $flag = (strpos($firstLine, 'newman') !== false && strpos($firstLine, 'error') == false && strpos($firstLine, 'failed') == false);
                                            $newmanTaskStatus = $flag ? "success":'error';
                                            $newmanRunStatus = $flag ?"success":'error';
                                            break; // 退出循环
                                        }
                                    }
                                    // 判断是否超过最大等待时间
                                    $currentTime = time();
                                    if ($currentTime - $startTime >= $maxWaitTime) {
                                        break;
                                    }
                                    // 等待一段时间再重新尝试读取文件
                                    sleep(1); // 可以根据实际情况调整等待时间
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
                        }
                        $this->customThingCache->delete($taskId . "_newman_process");
                    }
                }
            }
        }

        return $result;
    }

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
     */
    public function handlePutNewmanTasks(array $params, array $returnFields): array
    {
        $newmanTask = $this->entityManager->getRepository(NewmanTask::class)->findOneById($params['id']);

        // example
        if (array_key_exists('log', $params)) {
            $newmanTask->setLog($params["log"]);
        }
        if (array_key_exists('status', $params)) {
            if ($params["status"] == 'error'){
                $newmanTask->setActive(1);
            }
            $newmanTask->setStatus($params["status"]);
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
        $newmanTask->setActive(0);
        $this->entityManager->persist($newmanTask);
        $this->entityManager->flush();

        $newmanTaskDto = new NewmanTaskDto($newmanTask);
        return $newmanTaskDto->toArray($returnFields);
    }
}