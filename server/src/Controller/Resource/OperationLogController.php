<?php

namespace App\Controller\Resource;

use App\Service\OperationLogService;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class OperationLogController extends AbstractController
{
    private OperationLogService $operationLogService;

    public function __construct(
        OperationLogService $operationLogService
    )
    {
        $this->operationLogService = $operationLogService;
    }

    /**
     * @Route("/operation_log/type", name="获取日志类型", methods={"GET"})
     */
    public function getOperationLogType(): JsonResponse
    {
        $resultArray['data'] = $this->operationLogService->getOperationType(['id', 'name']);
        return new JsonResponse($resultArray);
    }

    /**
     * @Route("/operation_log", name="查询日志", methods={"GET"})
     */
    public function getOperationLog(Request $request): JsonResponse
    {
        $resultArray = array();
        $type = $request->query->get("type") ?? null;
        $createdAt = $request->query->get("created_at") ?? null;
        $start = $request->query->get("start") ?? null;
        $target = $request->query->get("target") ?? null;
        $status = $request->query->get("status") ?? null;
        $name = $request->query->get("name") ?? null;

        if ($createdAt){
            $resultArray['data'] = $this->operationLogService->getOperationLogByCreatedAt($createdAt,$type,$name);
            return new JsonResponse($resultArray);
        }

        if ($start && $target){
            $resultArray['data'] = $this->operationLogService->getOperationLogByStartAndTarget($start,$target);
            return new JsonResponse($resultArray);
        }

        $resultArray['data'] = $this->operationLogService->getOperationLogs();
        return new JsonResponse($resultArray);

    }
}