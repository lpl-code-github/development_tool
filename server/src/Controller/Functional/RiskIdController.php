<?php

namespace App\Controller\Functional;


use App\Factory\ExceptionFactory;
use App\Service\RiskidService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class RiskIdController extends AbstractController
{
    private RiskidService $riskidService;
    public function __construct(
        RiskidService $riskidService
    )
    {
        $this->riskidService = $riskidService;
    }

    /**
     * @Route("/clearCache", name="清除R1缓存", methods={"POST"})
     */
    public function clearCache(): JsonResponse
    {
        $resultArray = array();
        $resultArray['data']['handle'] = $this->riskidService->clearCache();
        return new JsonResponse($resultArray);
    }

    /**
     * @Route("/getApiInfo", name="获取RISKID所有API信息", methods={"GET"})
     */
    public function getApiInfo(): JsonResponse
    {
        $resultArray = array();
        $apiInfos = $this->riskidService->getApiInfos();

        $fileApiCount = 0;
        $resourceApiCount = 0;
        $functionalApiCount = 0;
        $authApiCount = 0;
        $realtimeApiCount = 0;
        $thirdPartyApiCount = 0;
        $othersApiCount = 0;

        $results = [];
        foreach ($apiInfos as $name => $item) {
            if ($name == '_preview_error'){
                continue;
            }
            if ($item['path'] == '/backup' || $item['path'] == '/reduction'){
                continue;
            }
            if(strpos($item['path'], '/file/') !== false){
                $fileApiCount ++;
            }elseif(strpos($item['path'], '/resource/') !== false){
                $resourceApiCount ++;
            }elseif(strpos($item['path'], '/functional/') !== false){
                $functionalApiCount ++;
            }elseif(strpos($item['path'], '/realtime/') !== false){
                $realtimeApiCount ++;
            }elseif(strpos($item['path'], '/auth/') !== false){
                $authApiCount ++;
            }elseif(strpos($item['path'], '/thirdParty/') !== false){
                $thirdPartyApiCount ++;
            }else {
                $othersApiCount ++;
            }

            $result = [
                'name' => $name,
                'path' => $item['path'],
                'host' => $item['host'],
                'scheme' => $item['scheme'],
                'method' => $item['method'],
                'controller' => $item['defaults']['_controller']
            ];
            $results[] = $result;
        }

        $resultArray['data']['sum']['total_num'] = count($results);
        $resultArray['data']['sum']['detail']['file'] = $fileApiCount;
        $resultArray['data']['sum']['detail']['resource'] = $resourceApiCount;
        $resultArray['data']['sum']['detail']['functional'] = $functionalApiCount;
        $resultArray['data']['sum']['detail']['realtime'] = $realtimeApiCount;
        $resultArray['data']['sum']['detail']['auth'] = $authApiCount;
        $resultArray['data']['sum']['detail']['thirdParty'] = $thirdPartyApiCount;
        $resultArray['data']['sum']['detail']['others'] = $othersApiCount;
        $resultArray['data']['route_results'] = $results;

        return new JsonResponse($resultArray);
    }

    /**
     * @Route("/getFileLists", name="获取RISKID所有Entity列表", methods={"GET"})
     * @throws \Exception
     */
    public function getFileLists(Request $request): JsonResponse
    {
        $type = $request->query->get('type') ?? null;
        if (!$type){
            throw ExceptionFactory::WrongFormatException("缺少参数type");
        }

        $entityLists = $this->riskidService->handleGetFileLists($type);
        return new JsonResponse($entityLists);
    }


    /**
     * @Route("/switchStatus", name="获取快捷开关操作", methods={"GET"})
     * @throws \Exception
     */
    public function switchStatus(): JsonResponse
    {
        $resultArray = array();
        $devEnvErrorMessageResult = array();
        $testEnvErrorMessageResult = array();
        $testEvnResult = array();
        $backApiResult = array();

        $devEnvErrorMessageResult['type'] = 'dev_env_error_message';
        $testEnvErrorMessageResult['type'] = 'test_env_error_message';
        $testEvnResult['type'] = 'test_env';
        $backApiResult['type'] = 'back_api';

        // 1.判断报错信息是否开启
        $devEnvErrorMessageResult["checked"]= $this->riskidService->getErrorMessage("dev");
        $testEnvErrorMessageResult["checked"]= $this->riskidService->getErrorMessage("test");

        // 2.判断riskid当前环境
        $riskidAppEnv = $this->riskidService->getAppEvn();
        if ($riskidAppEnv === 'dev') {
            $testEvnResult['checked'] = false;
        } elseif ($riskidAppEnv === 'test') {
            $testEvnResult['checked'] = true;
        } else {
            // APP_ENV is neither dev nor test
            $testEvnResult['checked'] = false;
            $testEvnResult['value'] = $riskidAppEnv;
        }

        $backApiResult['checked'] = $this->riskidService->getBackApiExists();

        $resultArray['data'][] = $backApiResult;
        $resultArray['data'][] = $devEnvErrorMessageResult;
        $resultArray['data'][] = $testEnvErrorMessageResult;
        $resultArray['data'][] = $testEvnResult;

        return new JsonResponse($resultArray);
    }

    /**
     * @Route("/quickSwitch", name="快捷开关操作", methods={"PUT"})
     * @throws \Exception
     */
    public function quickSwitch(Request $request): JsonResponse
    {
        $resultArray = array();

        $params = json_decode($request->getContent(),true);
        $data = $params['data'];
        $type = $data["type"] ?? null;
        $flag = (bool) $data["flag"] ?? null;

        if (!isset($type) || !isset($flag)) {
            throw ExceptionFactory::WrongFormatException("参数缺失");
        }
        $validTypes = ['dev_env_error_message', 'test_env_error_message', 'test_env', 'back_api'];
        if (!in_array($type, $validTypes)) {
            throw ExceptionFactory::WrongFormatException("type参数值不合法");
        }
        if (!is_bool($flag)){
            throw ExceptionFactory::WrongFormatException("flag参数只能是bool类型");
        }
        switch ($type){
            case 'test_env' :
                if ($flag){
                    $env = "test";
                } else {
                    $env = "dev";
                }
                $this->riskidService->editAppEvn($env);
                break;
            case 'back_api' :
                $this->riskidService->importOrRemoveBackApi($flag);
                break;
            case 'dev_env_error_message' :
                $this->riskidService->editErrorMessage('dev', $flag);
                break;
            case 'test_env_error_message' :
                $this->riskidService->editErrorMessage('test', $flag);
                break;
            default:
                break;
        }
        // 如果没有抛出异常就是true
        $resultArray['data']['handle'] =true;
        return new JsonResponse($resultArray);
    }
}