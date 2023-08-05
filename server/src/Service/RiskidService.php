<?php

namespace App\Service;

use App\Factory\ExceptionFactory;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;

class RiskidService
{
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    public function __construct(
        ParameterBagInterface $parameterBag
    )
    {
        $this->parameterBag = $parameterBag;
    }

    /**
     * 获取Riskid所有的api信息
     *
     * @return array
     */
    public function getApiInfos()
    {
        $process = Process::fromShellCommandline($this->parameterBag->get('debug_router_command'));
        // $process->setWorkingDirectory('/var/app/server/');// 测试用
        $process->setWorkingDirectory($this->parameterBag->get('riskid_code_path'));
        $process->run();

        return $process->isSuccessful()?  json_decode($process->getOutput(), true) : [];

    }

    /**
     * 清除RiskId的缓存
     *
     * @return bool
     */
    public function clearCache(): bool
    {
        $process = Process::fromShellCommandline($this->parameterBag->get('cache_clear_command'));
//         $process->setWorkingDirectory('/var/app/server/');// 测试用
        $process->setWorkingDirectory($this->parameterBag->get('riskid_code_path'));
        $process->run();
        return $process->isSuccessful();
    }

    /**
     * 获取Riskid的环境
     *
     * @return string
     */
    public function getAppEvn(): string
    {
        $dotenv = new Dotenv();
        $dotenv->load($this->parameterBag->get("riskid_env_path"));
        return $_ENV['APP_ENV'];
    }

    /**
     * 修改Riskid的环境
     *
     * @param $env
     * @return void
     * @throws \Exception
     */
    public function editAppEvn($env)
    {
        try {
            $envFile = $this->parameterBag->get("riskid_env_path");

            // 修改环境变量： 不能这样修改环境变量，因为本项目也是symfony，这样会把r1的evn设置成这里的环境变量
//            $dotenv = new Dotenv();
//            $dotenv->load($envFile);
//            $dotenv->populate(['APP_ENV' => $env], true);

            // 重写文件中的APP_ENV
            $envFileLines = file($envFile, FILE_IGNORE_NEW_LINES);
            foreach ($envFileLines as &$line) {
                if (strpos($line, 'APP_ENV=') === 0) {
                    $line = 'APP_ENV=' . $env;
                    break;
                }
            }
            file_put_contents($envFile, implode("\n", $envFileLines) . "\n");
        } catch (\Exception $exception) {
            throw ExceptionFactory::InternalServerException($exception->getMessage());
        }
    }

    /**
     * 修改Riskid开启报错信息
     *
     * @param string $env
     * @param bool $flag
     * @return bool
     * @throws \Exception
     */
    public function editErrorMessage(string $env, bool $flag): bool
    {
        $filePath = $this->parameterBag->get("riskid_exception_subscriber_php_path");
        try {
            $permission = 0644;
            chmod($filePath, $permission);

            // 重写文件中的$this->environment这一行
            $envFileLines = file($filePath, FILE_IGNORE_NEW_LINES);
            foreach ($envFileLines as &$line) {
                if (preg_match('/if\s*\(\s*\$this->environment\s*!==\s*[\'"]?dev[\'"]?/', $line) ||
                    preg_match('/if\s*\(\s*\$this->environment\s*==\s*[\'"]?dev[\'"]?/', $line)) {
                    if ($env == 'dev') {
                        $line = $flag ? '        if($this->environm ent !== \'dev\'){' : '        if($this->environment == \'dev\'){';
                    } elseif ($env == 'test') {
                        $line = $flag ? '        if($this->environment == \'dev\'){' : '        if($this->environment !== \'dev\'){';
                    }
                    break;
                }
            }
            file_put_contents($filePath, implode("\n", $envFileLines) . "\n");
            return true;
        } catch (\Exception $exception) {
            throw ExceptionFactory::InternalServerException($exception->getMessage());
        }
    }

    /**
     * 获取Riskid是否开启报错信息
     *
     * @param string $env env的类型
     * @return bool
     * @throws \Exception
     */
    public function getErrorMessage(string $env): bool
    {
        $fileContent = file_get_contents($this->parameterBag->get("riskid_exception_subscriber_php_path"));

        $flag = (preg_match('/\$this->environment\s*!==\s*[\'"]?dev[\'"]?/', $fileContent) &&
            !preg_match('/\$this->environment\s*==\s*[\'"]?dev[\'"]?/', $fileContent));

        if ($env == "dev") {
            return $flag;
        } elseif ($env == "test") {
            return !$flag;
        }
        throw ExceptionFactory::InternalServerException("参数 envType错误");
    }

    /**
     * 导入或移除Riskid中的TestController.php
     * @param bool $flag 如果是true则导入，否则移除
     * @return bool
     * @throws \Exception
     */
    public function importOrRemoveBackApi(bool $flag): bool
    {
        try {
            $target = $this->parameterBag->get('target_test_controller_path');
            $source = $this->parameterBag->get('source_test_controller_path');

            $filesystem = new Filesystem();
            if ($flag) {
                if (!$filesystem->exists($target)) {
                    $filesystem->copy(
                        BASE_PATH . $source,
                        $target
                    );
                }
            } else {
                if ($filesystem->exists($target)) {
                    $filesystem->remove($target);
                }
            }

            return true;
        } catch (\Exception $exception) {
            throw ExceptionFactory::InternalServerException($exception->getMessage());
        }
    }

    /**
     * 查看Riskid中是否存在TestController.php
     *
     * @return bool
     * @throws \Exception
     */
    public function getBackApiExists(): bool
    {
        try {
            $filesystem = new Filesystem();
            return $filesystem->exists($this->parameterBag->get('target_test_controller_path'));
        } catch (\Exception $exception) {
            throw ExceptionFactory::InternalServerException($exception->getMessage());
        }
    }

    /**
     * 获取Riskid某个目录下的所有文件
     * @param $type
     * @return array
     * @throws \Exception
     */
    public function handleGetFileLists($type): array
    {
        switch ($type){
            case "entity":
                $path = $this->parameterBag->get('risk_id_entity_path');
                 break;
            case "controller":
                $path = $this->parameterBag->get('risk_id_controller_path');
                break;
            default:
                throw ExceptionFactory::InternalServerException("type错误");
        }

        $finder = new Finder();
        // 因为新的riskid的controller分为了不同的目录，因此选择使用$finder去查找目录下所有文件，包括子目录
        $files = $finder->files()
            ->in($path)
            ->name('*.php');

        $result = [];

        foreach ($files as $file) {
            $result[] = $file->getBasename();
        }

        return $result;

    }
}