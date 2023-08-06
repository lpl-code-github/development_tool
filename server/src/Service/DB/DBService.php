<?php

namespace App\Service\DB;

use App\Factory\ExceptionFactory;
use PDO;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Process\Process;

class DBService
{
    private ParameterBagInterface $parameterBag;

    /**
     * @param ParameterBagInterface $parameterBag
     */
    public function __construct(
        ParameterBagInterface $parameterBag
    )
    {
        $this->parameterBag = $parameterBag;
    }

    /**
     * @return string[]
     */
    public function getDBInfo(): array
    {
        $envFile = $this->parameterBag->get("riskid_env_path");
        $dbHost = "";
        $dbUser = "";
        $dbPwd = "";
        $envFileLines = file($envFile, FILE_IGNORE_NEW_LINES);
        foreach ($envFileLines as $line) {
            if (strpos($line, 'DB_HOST=') === 0) {
                $dbHost = substr($line, strlen('DB_HOST='));
            }
            if (strpos($line, 'DB_USER=') === 0) {
                $dbUser = substr($line, strlen('DB_USER='));
            }
            if (strpos($line, 'DB_PWD=') === 0) {
                $dbPwd = substr($line, strlen('DB_PWD='));
            }
        }
        return array($dbHost, $dbUser, $dbPwd);
    }


    /**
     * 备份数据库
     * @param $dbHost
     * @param $dbUser
     * @param $dbPwd
     * @param $dbName
     * @param $exportPath
     * @return bool
     */
    public function mysqlDump($dbHost, $dbUser, $dbPwd, $dbName, $exportPath): bool
    {
        $command = "mysqldump -h " . $dbHost . " -u" . $dbUser . " -p" . $dbPwd . " " . $dbName . " > " . $exportPath;
        $process = Process::fromShellCommandline($command);
        $process->run();
        return $process->isSuccessful();
    }

    /**
     * @param string $dbHost
     * @param string $dbUser
     * @param string $dbPwd
     * @param $dbName
     * @return bool
     */
    public function deleteDb(string $dbHost, string $dbUser, string $dbPwd, $dbName): bool
    {
        $command = "mysql -h " . $dbHost . " -u " . $dbUser . " -p" . $dbPwd . " -e 'DROP DATABASE IF EXISTS " . $dbName."'";
        $process = Process::fromShellCommandline($command);
        $process->run();
        return $process->isSuccessful();
    }

    /**
     * @param string $dbHost
     * @param string $dbUser
     * @param string $dbPwd
     * @param $dbName
     * @return bool
     */
    public function createDb(string $dbHost, string $dbUser, string $dbPwd, $dbName): bool
    {
        $command = "mysql -h " . $dbHost . " -u " . $dbUser . " -p" . $dbPwd . " -e 'CREATE DATABASE " . $dbName . " CHARACTER SET latin1 COLLATE latin1_swedish_ci'";
        $process = Process::fromShellCommandline($command);
        $process->run();
        return  $process->isSuccessful();
    }

    /**
     * @param string $dbHost
     * @param string $dbUser
     * @param string $dbPwd
     * @param $dbName
     * @param $path
     * @return bool
     */
    public function importDb(string $dbHost, string $dbUser, string $dbPwd, $dbName, $path): bool
    {
        $command = "mysql -h " . $dbHost . " -u " . $dbUser . " -p" . $dbPwd . " " . $dbName . " < " . $path;
        $process = Process::fromShellCommandline($command);
        $process->run();
        return $process->isSuccessful();
    }

    /**
     * 获取数据库服务器所有的数据库列表
     * @return array
     * @throws \Exception
     */
    public function getDBList(): array
    {
        list($dbHost, $dbUser, $dbPwd) = $this->getDBInfo();

        $dsn = "mysql:host=" . $dbHost;

        $connection = new PDO($dsn, $dbUser, $dbPwd);
        $query = $connection->prepare('SHOW DATABASES');
        if (!$query->execute()) {
            throw ExceptionFactory::InternalServerException("查询语句执行错误：SHOW DATABASES");
        }
        $dbs = $query->fetchAll();

        $result = array();
        // 去除系统数据库以及本项目的数据库
        foreach ($dbs as $db){
            if (in_array($db['Database'],[
                "information_schema",
                "develop_tool_server",
                "mysql",
                "performance_schema",
                "sys"
            ])){
                continue;
            }
            $result[] = $db['Database'];
        }

        return $result;
    }
}