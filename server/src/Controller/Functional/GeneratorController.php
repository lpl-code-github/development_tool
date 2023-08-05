<?php

namespace App\Controller\Functional;

use App\Controller\BaseController;
use App\Factory\ExceptionFactory;
use App\Service\GeneratorService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GeneratorController extends BaseController
{
    private GeneratorService $generatorService;
    public function __construct(
        GeneratorService $generatorService
    )
    {
        $this->generatorService = $generatorService;
    }

    /**
     * @Route("/generatePostmanTest", name="生成POSTMAN测试", methods={"POST"})
     * @throws \Exception
     */
    public function executeGeneratePostmanTest(Request $request): Response
    {
        $params = json_decode($request->getContent(),true);

        // 测试用
        $this->validateNecessaryParameters($params,[
            'data'=>self::OBJECT_TYPE,
            'name'=>self::STRING_TYPE,
            'age'=>self::INT_TYPE,
            'balance'=>self::FLOAT_TYPE,
            'tags'=>self::ARRAY_TYPE,
            'null'=>self::NULL_TYPE,
            'flag'=>self::BOOL_TYPE,
        ]);
        $result = $this->generatorService->handleGeneratorPostmanTest($params);

        $response = new Response($result);
        $response->headers->set('Content-Type', 'text/javascript');

        return $response;
    }

    /**
     * @Route("/generateCode", name="生成Resource类型的接口代码", methods={"GET"})
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function executeGenerateCode(Request $request): Response
    {
        $entityFileName = $request->query->get('entity_name') ?? null;
        $type = $request->query->get('type') ?? null;
        if (!$entityFileName){
            throw ExceptionFactory::WrongFormatException("缺少参数entity_name");
        }
        if (!$type){
            throw ExceptionFactory::WrongFormatException("缺少参数type");
        }

        switch ($type){
            case "dto":
                $result = $this->generatorService->handleGeneratorDtoCode($entityFileName);
                break;
            case "factory":
                $result = $this->generatorService->handleGeneratorFactoryCode($entityFileName);
                break;
            case "controller":
                $result = $this->generatorService->handleGeneratorControllerCode($entityFileName);
                break;
            case "service":
                $result = $this->generatorService->handleGeneratorServiceCode($entityFileName);
                break;
            default:
                throw ExceptionFactory::WrongFormatException("参数type不支持");
        }

        $response = new Response($result);
        $response->headers->set('Content-Type', 'text/javascript');

        return $response;
    }

    /**
     * @Route("/generateSlateDoc", name="生成slate文档", methods={"GET"})
     */
    public function executeGenerateSlateDoc(Request $request): Response
    {
        $controller = $request->query->get('controller') ?? null;

        $result = $this->generatorService->handleGeneratorSlateDoc($controller);
        $response = new Response($result);
        $response->headers->set('Content-Type', 'text/javascript');

        return $response;
    }
}