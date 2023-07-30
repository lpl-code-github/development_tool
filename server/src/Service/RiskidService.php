<?php

namespace App\Service;

use App\Factory\ExceptionFactory;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Dotenv\Dotenv;
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
    public function getApiInfos(): array
    {
        $process = Process::fromShellCommandline($this->parameterBag->get('debug_router_command'));
        // $process->setWorkingDirectory('/var/app/server/');// 测试用
        $process->setWorkingDirectory('/var/app/r1/');
        $process->run();
        $output = $process->getOutput();
        return json_decode($output, true);
    }

    /**
     * 清除RiskId的缓存
     *
     * @return bool
     */
    public function clearCache(): bool
    {
        $process = Process::fromShellCommandline($this->parameterBag->get('cache_clear_command'));
        // $process->setWorkingDirectory('/var/app/server/');// 测试用
        $process->setWorkingDirectory('/var/app/r1/');
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

            // 修改环境变量
            $dotenv = new Dotenv();
            $dotenv->load($envFile);
            $dotenv->populate(['APP_ENV' => $env], true);

            // 重写文件中的APP_ENV
            $envFileLines = file($envFile, FILE_IGNORE_NEW_LINES);
            foreach ($envFileLines as &$line) {
                if (strpos($line, 'APP_ENV=') === 0) {
                    $line = 'APP_ENV=' . $env;
                    break;
                }
            }
            file_put_contents($envFile, implode("\n", $envFileLines) . "\n");
        }catch (\Exception $exception){
            throw ExceptionFactory::InternalServerException($exception->getMessage());
        }
    }

    /**
     * 获取Riskid是否开启报错信息
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
}