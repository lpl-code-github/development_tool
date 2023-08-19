<?php

namespace App\Service;

use App\Factory\ExceptionFactory;
use App\Service\Generator\GenerateApiTsInterfaceService;
use App\Service\Generator\GenerateApiTsServiceCodeService;
use App\Service\Generator\GenerateControllerCodeService;
use App\Service\Generator\GenerateDtoCodeService;
use App\Service\Generator\GenerateFactoryCodeService;
use App\Service\Generator\GenerateTsModalService;
use App\Service\Generator\GeneratePostmanTestService;
use App\Service\Generator\GenerateServiceCodeService;
use App\Service\Generator\GenerateSlateService;
use App\Utils\GenerateUtil;
use DateTime;
use ReflectionException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * 生成器的Service
 *
 *      对于生成Dto Factory Service Controller代码，
 *      先从RiskId中把Entity复制到本项目的TempEntity中
 *      然后获取一些变量等用于生成代码
 */
class GeneratorService
{
    private GeneratePostmanTestService $generatePostmanTestService;
    private GenerateDtoCodeService $generateDtoCodeService;
    private GenerateFactoryCodeService $generateFactoryCodeService;
    private GenerateControllerCodeService $generateControllerCodeService;
    private GenerateServiceCodeService $generateServiceCodeService;
    private GenerateSlateService $generateSlateService;
    private GenerateApiTsInterfaceService $generateApiTsInterfaceService;
    private GenerateApiTsServiceCodeService $generateApiTsServiceCodeService;
    private RiskidService $riskidService;
    private ParameterBagInterface $parameterBag;
    private GenerateTsModalService $generateTsModalService;

    public function __construct(
        GeneratePostmanTestService      $generatePostmanTestService,
        GenerateDtoCodeService          $generateDtoCodeService,
        GenerateFactoryCodeService      $generateFactoryCodeService,
        GenerateControllerCodeService   $generateControllerCodeService,
        GenerateServiceCodeService      $generateServiceCodeService,
        GenerateSlateService            $generateSlateService,
        GenerateApiTsInterfaceService   $generateApiTsInterfaceService,
        GenerateApiTsServiceCodeService $generateApiTsServiceCodeService,
        RiskidService                   $riskidService,
        ParameterBagInterface           $parameterBag,
        GenerateTsModalService $generateTsModalService
    )
    {
        $this->generatePostmanTestService = $generatePostmanTestService;
        $this->generateDtoCodeService = $generateDtoCodeService;
        $this->generateFactoryCodeService = $generateFactoryCodeService;
        $this->generateControllerCodeService = $generateControllerCodeService;
        $this->generateServiceCodeService = $generateServiceCodeService;
        $this->generateSlateService = $generateSlateService;
        $this->generateApiTsInterfaceService = $generateApiTsInterfaceService;
        $this->generateApiTsServiceCodeService = $generateApiTsServiceCodeService;
        $this->riskidService = $riskidService;
        $this->parameterBag = $parameterBag;
        $this->generateTsModalService = $generateTsModalService;
    }

    /**
     * 生成postman test
     * @param array $data
     * @return string
     */
    public function handleGeneratorPostmanTest(array $data): string
    {
        return $this->generatePostmanTestService->generatorPostmanTest($data);
    }

    /**
     * 生成ts的Api Interface属性校验代码
     * @param array $data
     * @param $name
     * @param $suffix
     * @param int $allowDepth
     * @return string
     */
    public function handleGenerateApiTsInterface(array $data, $name, $suffix, int $allowDepth = 3): string
    {
        return $this->generateApiTsInterfaceService->generateSimpleApiTsInterface($data, $name??'Example', $suffix, true, $allowDepth);
    }

    /**
     * 生成dto代码
     * @throws ReflectionException
     * @throws \Exception
     */
    public function handleGeneratorDtoCode($data): string
    {
        $filesystem = new Filesystem();
        $destFilePath = $this->copyRiskIdEntity($data, $filesystem);

        try {
            $dtoCode = $this->generateDtoCodeService->generateDtoCode(
                'App\TempEntity\\' . $this->removePhpExtension($data)
            );
            $filesystem->remove($destFilePath); // 操作完成后删除复制来的文件
        } catch (\Exception $exception) {
            $filesystem->remove($destFilePath);
            throw $exception;
        }

        return $dtoCode;
    }

    /**
     * 生成Factory代码
     * @param $data
     * @return string
     * @throws ReflectionException
     * @throws \Exception
     */
    public function handleGeneratorFactoryCode($data): string
    {
        $filesystem = new Filesystem();
        $destFilePath = $this->copyRiskIdEntity($data, $filesystem);

        try {
            $factoryCode = $this->generateFactoryCodeService->generateFactoryCode(
                'App\TempEntity\\' . $this->removePhpExtension($data)
            );
            $filesystem->remove($destFilePath); // 操作完成后删除复制来的文件
        } catch (\Exception $exception) {
            $filesystem->remove($destFilePath);
            throw $exception;
        }

        return $factoryCode;
    }

    /**
     * 生成Controller代码
     * @param $data
     * @return string
     * @throws ReflectionException
     * @throws \Exception
     */
    public function handleGeneratorControllerCode($data): string
    {
        $filesystem = new Filesystem();
        $destFilePath = $this->copyRiskIdEntity($data, $filesystem);

        try {
            $controllerCode = $this->generateControllerCodeService->generateControllerCode(
                'App\TempEntity\\' . $this->removePhpExtension($data)
            );
            $filesystem->remove($destFilePath);  // 操作完成后删除复制来的文件
        } catch (\Exception $exception) {
            $filesystem->remove($destFilePath);
            throw $exception;
        }

        return $controllerCode;
    }

    /**
     * 生成Service代码
     * @param $data
     * @return string
     * @throws ReflectionException
     * @throws \Exception
     */
    public function handleGeneratorServiceCode($data): string
    {
        $filesystem = new Filesystem();
        $destFilePath = $this->copyRiskIdEntity($data, $filesystem);

        try {
            $serviceCode = $this->generateServiceCodeService->generateServiceCode(
                'App\TempEntity\\' . $this->removePhpExtension($data)
            );
            $filesystem->remove($destFilePath);  // 操作完成后删除复制来的文件
        } catch (\Exception $exception) {
            $filesystem->remove($destFilePath);
            throw $exception;
        }

        return $serviceCode;
    }

    /**
     * 生成slate文档
     * @param $controller
     * @return string
     */
    public function handleGeneratorSlateDoc($controller): string
    {
        if (!$controller) {
            return $this->generateSlateService->generateDefaultSlate();
        }

        $apiInfos = $this->riskidService->getApiInfos();
        $controllerName = $this->removePhpExtension($controller);// 去掉.php的controller名称
        $results = [];
        foreach ($apiInfos as $name => $item) {
            if ($name == '_preview_error') {
                continue;
            }
            if (strpos($item['defaults']['_controller'], $controllerName) !== false) {
                $result = [
                    'name' => $name,
                    'path' => $item['path'],
                    'method' => $item['method'],
                ];
                $results[] = $result;
            }
        }

        return $this->generateSlateService->generateSlateByController($controllerName, $results);

    }


    /**
     * 生成TsService
     * @param $controller
     * @param bool $all
     * @return string|null
     * @throws \Exception
     */
    public function handleGenerateApiTsServiceCode($controller): ?string
    {
        $apiInfos = $this->riskidService->getApiInfos();
        $controllerName = $this->removePhpExtension($controller);// 去掉.php的controller名称

        $results=array();
        foreach ($apiInfos as $name => $item) {
            if ($name == '_preview_error') {
                continue;
            }
            if (strpos($item['defaults']['_controller'], $controllerName) !== false) {
                $controllerName = $this->removePhpExtension($controller);// 去掉.php的controller名称
                $result = [
                    'name' => $name,
                    'path' => $item['path'],
                    'method' => $item['method'],
                    'function' => explode("::", $item['defaults']['_controller'])[1]
                ];
                $results[] = $result;
            }
        }
        return $this->generateApiTsServiceCodeService->generateTsServiceCodeByController($controllerName, $results);
    }

    /**
     * 根据全部api生成ts的ApiTsService
     * @return string
     * @throws \Exception
     */
    public function handleGenerateAllApiTsService(): string
    {
        $apiInfos = $this->getGroupApiInfos();

        $currentDateTime = new DateTime();
        $currentDateTime->modify('+8 hours');
        $formattedDateTime = $currentDateTime->format('YmdHis');
//        $tmpPath = BASE_PATH . "/resource/tmp/" . $formattedDateTime . '/';
        $tmpPath = "/tmp/" . $formattedDateTime . '/';

        foreach ($apiInfos as $folder => $controllerInfo) {
            // 创建File、Function、Resource等一级目录
            $typeFolder = $tmpPath . GenerateUtil::lowerFirst($folder);
            mkdir($typeFolder, 0777, true);
            // 在一级目录下创建controller的二级目录

            foreach ($controllerInfo as $controllerName => $endpoints) {
                $objectName = GenerateUtil::removeController($controllerName);
                $objectNameLowerFirst = GenerateUtil::lowerFirst($objectName); // 转为小写
                $controllerFolder = $typeFolder . '/' . $objectNameLowerFirst;

                // 创建controller的二级目录
                mkdir($controllerFolder, 0777, true);

                /**
                 * 1. 先生成modal
                 */
                $requestModalSuffix = 'Param';
                $responseModalSuffix = 'Response';
                // 1.1 请求体的modal文件
                $requestModalResult = $this->generateTsModalService->getModalResult($endpoints, $requestModalSuffix, 'request');
                $requestModelFilePath = $controllerFolder . "/api-$objectNameLowerFirst.request.model.ts";
                $requestModelFile = fopen($requestModelFilePath, 'w');
                if (!$requestModelFile) {
                    throw ExceptionFactory::InternalServerException("无法创建文件,$requestModelFile");
                }
                fwrite($requestModelFile, $requestModalResult); // 写入文件内容
                fclose($requestModelFile);
                // 1.2 响应体modal的文件
                $responseModalResult = $this->generateTsModalService->getModalResult($endpoints, $responseModalSuffix,'response');
                $responseModelFilePath = $controllerFolder . "/api-$objectNameLowerFirst.response.model.ts";
                $responseModelFile = fopen($responseModelFilePath, 'w');
                if (!$responseModelFile) {
                    throw ExceptionFactory::InternalServerException("无法创建文件,$responseModelFile");
                }
                fwrite($responseModelFile, $responseModalResult); // 写入文件内容
                fclose($responseModelFile);


                // 生成当前controller的所有api service代码
                $tsServiceCode = $this->generateApiTsServiceCodeService->generateTsServiceCodeByController($controllerName, $endpoints,true, $requestModalSuffix, $responseModalSuffix);
                $serviceFilePath = $controllerFolder . "/api-$objectNameLowerFirst.service.ts"; // 文件路径
                $serviceFile = fopen($serviceFilePath, 'w');
                if (!$serviceFile) {
                    throw ExceptionFactory::InternalServerException("无法打开文件,$serviceFile");
                }
                fwrite($serviceFile, $tsServiceCode); // 写入文件内容
                fclose($serviceFile); // 关闭文件
            }
        }

        return $tmpPath;
    }

    /**
     * 删除.php后缀
     * @param $string
     * @return false|mixed|string
     */
    function removePhpExtension($string)
    {
        $extension = pathinfo($string, PATHINFO_EXTENSION);
        if ($extension === 'php') {
            return substr($string, 0, -4);
        }
        return $string;
    }

    /**
     * 拷贝Riskid的Entity到本项目的TempEntity
     * @param $fileName
     * @param Filesystem $filesystem
     * @return string
     * @throws \Exception
     */
    private function copyRiskIdEntity($fileName, Filesystem $filesystem): string
    {
        // 源文件路径
        $srcFilePath = $this->parameterBag->get('risk_id_entity_path') . $fileName;
        // 目标文件路径
        $destFilePath = BASE_PATH . '/src/TempEntity/' . $fileName;


        if (!$filesystem->exists($srcFilePath)) {
            throw ExceptionFactory::NotFoundException('实体类文件未找到： ' . $srcFilePath);
        }

        // 复制文件
        $filesystem->copy($srcFilePath, $destFilePath, true);

        // 修改目标文件的命名空间
        $content = file_get_contents($destFilePath);
        $content = str_replace('namespace App\Entity;', 'namespace App\TempEntity;', $content);
        file_put_contents($destFilePath, $content);

        usleep(500000);// 防止文件复制太慢 休眠0.5秒

        return $destFilePath;
    }

    /**
     * 获取Riskid所有的api信息,通过目录和controller分组
     *
     * @return array
     */
    public function getGroupApiInfos(): array
    {
        $results = [];
        $apiInfos = $this->riskidService->getApiInfos();

        foreach ($apiInfos as $name => $item) {
            if (
                $name == 'backup' ||
                $name == 'reduction' ||
                $name == '_preview_error'
            ) {
                continue;
            }
            $_controller = $item['defaults']['_controller'];

            $start = strrpos($_controller, '\\') + 1;
            $end = strpos($_controller, '::');
            $controller = substr($_controller, $start, $end - $start);

            $folder = explode('\\', $_controller)[2];

            $results[$folder][$controller][] = [
                'name' => $name,
                'path' => $item['path'],
                'method' => $item['method'],
                'function' => explode("::", $item['defaults']['_controller'])[1]
            ];
        }
        return $results;
    }
}
