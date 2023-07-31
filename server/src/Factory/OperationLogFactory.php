<?php

namespace App\Factory;

use App\Entity\OperationLog;
use App\Service\OperationLogService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class OperationLogFactory
{
    private OperationLogService $operationLogService;

    /**
     * @param OperationLogService $operationLogService
     */
    public function __construct(OperationLogService $operationLogService)
    {
        $this->operationLogService = $operationLogService;
    }

    /**
     * 创建 OperationLog 实例
     *
     * @param Request $request
     * @param Response $response
     * @param null $errorMessage
     * @return OperationLog|null
     */
    public function create(Request $request, Response $response, $errorMessage = null): ?OperationLog
    {
        // 响应状态码
        $statusCode = $response->getStatusCode();
        // 响应体
        $responseData = json_decode($response->getContent(), true);
        // @Route注解写的name
        $actionName = $request->attributes->get('_route');
        // 请求体
        $requestBody = json_decode($request->getContent(), true);
        // 请求方法
        $requestMethod = $request->getMethod();
        // 接口名
        $sep_array = explode('/index.php', $_SERVER['PHP_SELF']);
        if ($sep_array[0] != '') {
            $temp_array = explode($sep_array[0], $request->getRequestUri());
            $uri = end($temp_array);
        } else {
            $uri = $request->getRequestUri();
        }

        // 接口操作状态
        if ($statusCode == 200) {
            if (isset($responseData['data']) && array_key_exists('handle', $responseData['data'])) {
                $status = ($responseData['data']['handle'] == true);
            } else {
                $status = true;
            }
        } else {
            $status = false;
        }

        // 接口操作类型
        $operationType = $this->operationLogService->getOperationType(['name'], $uri)[0]['name'];

        $operationLog = new OperationLog();
        $operationLog->setName($actionName);
        $operationLog->setMethod($requestMethod);
        $operationLog->setStatusCode($statusCode);
        if ($errorMessage){
            $operationLog->setMessage($errorMessage);
        }
        $operationLog->setRequestBody($requestBody);
        $operationLog->setUrl($uri);
        $operationLog->setType($operationType);
        $operationLog->setStatus($status ? 'successful' : 'failure');
        return $operationLog;
    }
}
