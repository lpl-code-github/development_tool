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

        // 接口名
        $sep_array = explode('/index.php', $_SERVER['PHP_SELF']);
        if ($sep_array[0] != '') {
            $temp_array = explode($sep_array[0], $request->getRequestUri());
            $uri = end($temp_array);
        } else {
            $uri = $request->getRequestUri();
        }
        $uri = strstr($uri, '?', true);

        // 响应体
        $responseData = json_decode($response->getContent(), true);

        // $actionName默认为@Route注解写的name
        // 有几个接口需要重写$actionName
        $rewriteActionName = $this->rewriteActionName($uri, $request);
        $actionName = ($rewriteActionName != null) ? $rewriteActionName : $request->attributes->get('_route');

        // 请求体
        $requestBody = json_decode($request->getContent(), true);
        // 请求方法
        $requestMethod = $request->getMethod();

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
        if ($errorMessage) {
            $operationLog->setMessage($errorMessage);
        }
        $operationLog->setRequestBody($requestBody);
        $operationLog->setUrl($uri);
        $operationLog->setType($operationType);
        $operationLog->setStatus($status ? 'successful' : 'failure');
        return $operationLog;
    }

    /**
     * 自定义一些接口的ActionName
     * @param $uri
     * @param $request
     * @return string|null
     */
    private function rewriteActionName($uri, $request): ?string
    {
        $requestData = json_decode($request->getContent(), true);
        if ($uri == "/functional/quickSwitch") {
            return $this->handleQuickSwitchRewriteActionName($requestData);
        }elseif ($uri == "/functional/uploadFile"){
            return $this->handleUploadFileRewriteActionName($request);
        }
        return null;
    }

    /**
     * 重写/functional/quickSwitch接口日志的ActionName
     * @param $requestData
     * @return string|null
     */
    function handleQuickSwitchRewriteActionName($requestData): ?string
    {
        $type = $requestData['data']['type'] ?? null;
        $flag = $requestData['data']['flag'] ?? null;
        if ($type) {
            switch ($type) {
                case 'test_env' :
                    return $flag ? "打开test环境" : "关闭test环境";
                case 'back_api' :
                    return $flag ? "导入备份接口" : "取消导入备份接口";
                case 'dev_env_error_message' :
                    return $flag ? "打开开发环境报错信息" : "关闭开发环境报错信息";
                case 'test_env_error_message' :
                    return $flag ? "打开测试环境报错信息" : "关闭关闭环境报错信息";
                default:
                    return null;
            }
        }
        return null;
    }

    /**
     * 重写/functional/uploadFile接口日志的ActionName
     * @param $request
     * @return string|null
     */
    function handleUploadFileRewriteActionName(Request $request): ?string
    {
        $type = $request->query->get('type') ?? null;
        $file = $request->files->get('file');

        $extension = $file->getClientOriginalExtension();
        $filename = substr($file->getClientOriginalName(), 0, 255 - strlen($extension) - 1);

        switch ($type) {
            case "script":
                return "上传一个脚本文件：".$filename;
            default:
                return null;
        }
    }
}
