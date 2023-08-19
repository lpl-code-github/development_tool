<?php

namespace App\Controller\Functional;

use App\Controller\BaseController;
use App\Factory\ExceptionFactory;
use App\Service\GeneratorService;
use DateTime;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use ZipArchive;

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

        // 测试校验用
//        $this->validateNecessaryParameters($params,[
//            'data'=>self::OBJECT_TYPE,
//            'name'=>self::STRING_TYPE,
//            'age'=>self::INT_TYPE,
//            'balance'=>self::FLOAT_TYPE,
//            'tags'=>self::ARRAY_TYPE,
//            'null'=>self::NULL_TYPE,
//            'flag'=>self::BOOL_TYPE,
//        ]);
        $result = $this->generatorService->handleGeneratorPostmanTest($params);

        $response = new Response($result);
        $response->headers->set('Content-Type', 'text/javascript');

        return $response;
    }

    /**
     * @Route("/generateTsInterface", name="生成Api响应的TypeScript Interface", methods={"POST"})
     * @throws \Exception
     */
    public function executeGenerateApiTsInterface(Request $request): Response
    {
        $name = $request->query->get('name')??null;
        $suffix = $request->query->get('suffix') ?? "Response";
        $allowDepth = (int) $request->query->get('allow_depth');
        $params = json_decode($request->getContent(),true);

        if ($allowDepth == 0){
            $allowDepth = 3;
        }
        $result = $this->generatorService->handleGenerateApiTsInterface($params, $name, $suffix, $allowDepth);

        $response = new Response($result);
        $response->headers->set('Content-Type', 'text/javascript');

        return $response;
    }

    /**
     * @Route("/generateTsService", name="生成Ts调用api的service", methods={"GET"})
     * @throws \Exception
     */
    public function executeGenerateApiTsService(Request $request): Response
    {
        $controller = $request->query->get('controller') ?? null;
        $all = $request->query->get('all') ?? null;
        if ($controller == null && $all == null){
            throw ExceptionFactory::WrongFormatException("缺少参数");
        }
        if ($controller != null && $controller != "") { // 单个code
            $result = $this->generatorService->handleGenerateApiTsServiceCode($controller);

            $response = new Response($result);
            $response->headers->set('Content-Type', 'text/javascript');
            return $response;
        }
        if ($all){ // 全部 返回文件
            $sourceFolder = $this->generatorService->handleGenerateAllApiTsService();
            $zipFile = $sourceFolder.'.zip';
            // 创建并打开 zip 文件
            $zip = new ZipArchive();
            if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                throw ExceptionFactory::InternalServerException("无法创建压缩文件");
            }

            // 添加文件夹内容到 zip 文件
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($sourceFolder),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $name => $file) {
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($sourceFolder));

                    $zip->addFile($filePath, $relativePath);
                }
            }
            $zip->close();

            $currentDateTime = new DateTime();
            $currentDateTime->modify('+8 hours');
            $formattedDateTime = $currentDateTime->format('YmdHis');

            $response = new BinaryFileResponse($zipFile);
            $response->headers->set('Content-Type', 'application/zip');
            $response->setContentDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                "$formattedDateTime-generate-all-ts-service.zip"
            );

            return $response;
        }
        return new Response();
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