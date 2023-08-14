<?php

namespace App\Controller;

use App\Factory\ExceptionFactory;
use PHPUnit\Util\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

class PostmanTestController extends AbstractController
{
    const  SQL_FILE_PREFIX = 'database_';
    const  SQL_FILE_SUFFIX = '.sql';
    private $environment;

    public function __construct(
        KernelInterface $kernel
    )
    {
        $this->environment = $kernel->getEnvironment();
    }
    /**
     * @Route("/backup")
     */
    public function exportSQL(): Response
    {
        $dbHost = $_ENV['DB_HOST'];
        $dbUser = $_ENV['DB_USER'];
        $dbPwd = $_ENV['DB_PWD'];
        $dbName = $_ENV['DB_NAME'];

        if ($this->environment !== "test"){
            return new Response("Bad request: access forbidden!",401);
        }

        // 在目录名中添加日期时间戳，以便创建唯一的文件名
        $filePath= BASE_PATH . '/postman/' . self::SQL_FILE_PREFIX . date('YmdHis') . self::SQL_FILE_SUFFIX;


        // 导出 SQL 文件
        exec(sprintf('mysqldump -h %s -u%s -p%s %s > %s', $dbHost, $dbUser, $dbPwd, $dbName, $filePath), $output, $status);
        if ($status !== 0) {
            throw new Exception(sprintf('Failed to export the SQL file "%s".', $filePath));
        }

        return new Response();
    }

    /**
     * @Route("/reduction")
     * @throws \Exception
     */
    public function importSQL(): Response
    {
        $dbHost = $_ENV['DB_HOST'];
        $dbUser = $_ENV['DB_USER'];
        $dbPwd = $_ENV['DB_PWD'];
        $dbName = $_ENV['DB_NAME'];

        if ($this->environment !== "test"){
            return new Response("Bad request: access forbidden!",401);
        }

        $sqlFile = $this->getSQLFile();
        if ($sqlFile == null) {
            throw ExceptionFactory::InternalServerException('SQL file not found.');
        }
        $filePath= BASE_PATH . '/postman/' . $sqlFile;

        exec(sprintf('mysql -h %s -u%s -p%s -e "DROP DATABASE IF EXISTS %s"', $dbHost, $dbUser, $dbPwd, $dbName), $output, $status);
        if ($status !== 0) {
            throw ExceptionFactory::InternalServerException(sprintf('Failed to drop database `%s`.', $dbName));
        }

        exec(sprintf('mysql -h %s -u%s -p%s -e "CREATE DATABASE %s CHARACTER SET latin1 COLLATE latin1_swedish_ci"', $dbHost, $dbUser, $dbPwd, $dbName), $output, $status);
        if ($status !== 0) {
            throw ExceptionFactory::InternalServerException(sprintf('Failed to create database `%s`.', $dbName));
        }

        exec(sprintf('mysql -h %s -u%s -p%s %s < %s', $dbHost, $dbUser, $dbPwd, $dbName, $filePath), $output, $status);
        if ($status !== 0) {
            throw new \RuntimeException(sprintf('Failed to import the SQL file "%s".', $filePath));
        }

        unlink($filePath);

        return new Response();
    }

    private function getSQLFile(){
        $files = scandir(BASE_PATH .  '/postman/');

        $newestFileDate = 0;
        $newestFileName = '';

        foreach ($files as $file) {
            if (substr($file, 0, 9) === 'database_' && substr($file, -4) === '.sql') {
                $dateString = substr($file, 9, 14);
                $fileDate = strtotime($dateString);
                if ($fileDate > $newestFileDate) {
                    $newestFileDate = $fileDate;
                    $newestFileName = $file;
                }
            }
        }

        if (!$newestFileName) {
            return null;
        }

        return $newestFileName;
    }

}
