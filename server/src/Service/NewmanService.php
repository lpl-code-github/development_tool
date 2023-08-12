<?php

namespace App\Service;

use App\Factory\ExceptionFactory;
use DateTime;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Process\Process;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;


class NewmanService
{
    private ParameterBagInterface $parameterBag;
    private CacheInterface $customThingCache;

    public function __construct(
        CacheInterface        $customThingCache,
        ParameterBagInterface $parameterBag

    )
    {
        $this->customThingCache = $customThingCache;
        $this->parameterBag = $parameterBag;

    }

    /**
     *
     * 这是测试任务的第四步，进行newman测试
     *
     * @throws \Exception
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function handleRunNewman($taskId): bool
    {
        // 检查缓存中是否存在 Process 实例
        $processKey = $taskId . "_newman_process";

        $this->customThingCache->get($processKey, function (ItemInterface $item) use ($taskId) {
            // 保存环境变量
            $postmanEnv = $this->getPostmanEnv();
            $postmanEnvJson = json_encode($postmanEnv);
            $tmpEnvFilePath = BASE_PATH . $this->parameterBag->get("temp_postman_env") . "temp_env.json";
            try {
                file_put_contents($tmpEnvFilePath, $postmanEnvJson);
            } catch (\Exception $exception) {
                throw ExceptionFactory::InternalServerException("生成postman临时文件出错");
            }

            $postmanCollectionUrl = $this->parameterBag->get('postman_collection_url');
            $postmanCollectionUrl = $this->parameterBag->get('test_postman_collection_url');//测试用
            $newmanReportPath = BASE_PATH . $this->parameterBag->get("newman_report_path");
            $newmanCliOutputPath = BASE_PATH . $this->parameterBag->get("newman_cli_output");

            $currentDateTime = new DateTime();
            $currentDateTime->modify('+8 hours');
            $formattedDateTime = $currentDateTime->format('YmdHis');
            $htmlReportPath = $newmanReportPath . $formattedDateTime . ".html";
            $ExcelReportPath = $newmanReportPath . $formattedDateTime . ".csv";
            $cliOutputPath = $newmanCliOutputPath . $formattedDateTime . ".txt";

            $command = "nohup newman run '"
                . $postmanCollectionUrl
                . "' -e '"
                . $tmpEnvFilePath
                . "' -r cli,htmlextra,csv "
                . "--reporter-htmlextra-export " . $htmlReportPath . " "
                . "--reporter-csv-export " . $ExcelReportPath . " "
                . "> " . $cliOutputPath
                . " 2>&1 &";

            $process = Process::fromShellCommandline($command);
            $process->setOptions(['create_new_console' => true]);
            $process->run();
            $pid = $process->getPid();

            $processInfo = [
                'pid' => $pid,
                'html_report_path' => $htmlReportPath,
                'excel_report_path' => $ExcelReportPath,
                'cli_output_path' => $cliOutputPath,
            ];
            $item->set($processInfo);
            $item->expiresAfter(null);
            return $processInfo;
        });

        return true;
    }

    /**
     * @throws \Exception
     */
    public function getPostmanEnv()
    {
        $postmanEnvUrl = $this->parameterBag->get('postman_env_url');

        $curl = curl_init();
        try {
            curl_setopt_array($curl, array(
                CURLOPT_URL => $postmanEnvUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
            ));

            $response = curl_exec($curl);
            curl_close($curl);

            if ($response) {
                $postmanEnv = json_decode($response, true);
                if (array_key_exists("environment", $postmanEnv)) {
                    foreach ($postmanEnv["environment"] as $key => &$item) {
                        if ($key == "values") {
                            foreach ($item as &$value) {
                                if ($value['key'] == 'api_url') {
                                    $value['value'] = "http://localhost/r1/public";
                                }
                            }
                        }
                    }
                }
                return $postmanEnv;
            } else {
                throw ExceptionFactory::InternalServerException("获取postman环境变量文件错误");
            }
        } catch (\Exception $exception) {
            throw ExceptionFactory::InternalServerException("请求postman环境变量文件错误");
        }
    }

}