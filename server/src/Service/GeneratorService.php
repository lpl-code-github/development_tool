<?php

namespace App\Service;

use App\Factory\ExceptionFactory;
use App\Service\Generator\GenerateControllerCodeService;
use App\Service\Generator\GenerateDtoCodeService;
use App\Service\Generator\GenerateFactoryCodeService;
use App\Service\Generator\GeneratePostmanTestService;
use App\Service\Generator\GenerateServiceCodeService;
use ReflectionException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;

class GeneratorService
{
    private GeneratePostmanTestService $generatePostmanTestService;
    private GenerateDtoCodeService $generateDtoCodeService;
    private GenerateFactoryCodeService $generateFactoryCodeService;
    private GenerateControllerCodeService $generateControllerCodeService;
    private GenerateServiceCodeService $generateServiceCodeService;
    private ParameterBagInterface $parameterBag;

    public function __construct(
        GeneratePostmanTestService    $generatePostmanTestService,
        GenerateDtoCodeService        $generateDtoCodeService,
        GenerateFactoryCodeService    $generateFactoryCodeService,
        GenerateControllerCodeService $generateControllerCodeService,
        GenerateServiceCodeService    $generateServiceCodeService,
        ParameterBagInterface         $parameterBag
    )
    {
        $this->generatePostmanTestService = $generatePostmanTestService;
        $this->generateDtoCodeService = $generateDtoCodeService;
        $this->generateFactoryCodeService = $generateFactoryCodeService;
        $this->generateControllerCodeService = $generateControllerCodeService;
        $this->generateServiceCodeService = $generateServiceCodeService;
        $this->parameterBag = $parameterBag;
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
}