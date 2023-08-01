<?php

namespace App\Service;

use App\Dto\OperationLogDto;
use App\Entity\OperationLog;
use Doctrine\ORM\EntityManagerInterface;

class OperationLogService
{
    const OPERATION_TYPE_ARRAY = [
        [
            "id" => "1",
            "name" => "快捷开关",
            "route" => [
                "/functional/clearCache",
                "/functional/quickSwitch"
            ]

        ],
        [
            "id" => "2",
            "name" => "生成器",
            "route" => [
                "/functional/generatePostmanTest",
            ]
        ]
    ];

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    /**
     * 通过$route查找指定的操作类型，如果不传默认返回全部
     *
     * @param array|null $field
     * @param string|null $route
     * @return array|array[]|null
     */
    public function getOperationType(array $field = null, string $route = null): ?array
    {
        $result = array();

        if ($field == null && $route == null) {
            $result = self::OPERATION_TYPE_ARRAY;
        }elseif($field !== null && $route == null){
            foreach (self::OPERATION_TYPE_ARRAY as $item) {
                $operation = [];
                foreach ($field as $f) {
                    if (isset($item[$f])) {
                        $operation[$f] = $item[$f];
                    }
                }
                $result[] = $operation;
            }
        }else{
            foreach (self::OPERATION_TYPE_ARRAY as $item) {
                if (in_array($route, $item['route'])) {
                    // 根据$field返回对应的数据
                    $operation = [];
                    foreach ($field as $f) {
                        if (isset($item[$f])) {
                            $operation[$f] = $item[$f];
                        }
                    }
                    $result[] = $operation;
                    break;
                }
            }
        }

        return $result;
    }


    /**
     * 保存 OperationLog 实例到数据库
     *
     * @param OperationLog $operationLog
     */
    public function saveOperationLog(OperationLog $operationLog): void
    {
        $this->entityManager->persist($operationLog);
        $this->entityManager->flush();
    }

    /**
     * 查询全部
     *
     * @return array
     */
    public function getOperationLogs(): array
    {
        $resultArray = array();
        $operationLogs = $this->entityManager->getRepository(OperationLog::class)->findAll();
        foreach ($operationLogs as $operationLog){
            $operationLogDto = new OperationLogDto($operationLog);
            $toArray = $operationLogDto->toArray(['type', 'name', 'status', 'status_code', 'message', 'created_at']);
            $resultArray[] = $toArray;
        }
        return $resultArray;
    }

    /**
     * 通过CreatedAt查询日志
     *
     * @param $createdAt
     * @param $type
     * @param $name
     * @return array
     */
    public function getOperationLogByCreatedAt($createdAt, $type, $name): array
    {
        $resultArray = array();

        $filed['created_at'] = $createdAt;
        if ($type){
            $filed['type'] = $type;
        }
        if ($name){
            $filed['name'] = $name;
        }

        $operationLogs = $this->entityManager->getRepository(OperationLog::class)->findObjArrayByFiled($filed);
        foreach ($operationLogs as $operationLog){
            $operationLogDto = new OperationLogDto($operationLog);
            $toArray = $operationLogDto->toArray(['type', 'name', 'status', 'status_code', 'message', 'created_at']);
            $resultArray[] = $toArray;
        }
        return $resultArray;
    }

    /**
     * 通过startDate和EndDate查询日期范围日志
     *
     * @param string $start
     * @param string $target
     * @return array
     */
    public function getOperationLogByStartAndTarget(string $start, string $target): array
    {
        $resultArray = array();

        $filed = array();
        if ($start){
            $filed['start'] = $start;
        }
        if ($target){
            $filed['target'] = $target;
        }

        $operationLogs = $this->entityManager->getRepository(OperationLog::class)->findObjArrayByFiled($filed);
        foreach ($operationLogs as $operationLog){
            $operationLogDto = new OperationLogDto($operationLog);
            $toArray = $operationLogDto->toArray(['type', 'name', 'status', 'status_code', 'message', 'created_at']);
            $resultArray[] = $toArray;
        }
        return $resultArray;
    }
}