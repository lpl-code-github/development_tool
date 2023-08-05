<?php

namespace App\Service;

use App\Factory\DatabaseBackupFactory;
use App\Factory\ExceptionFactory;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\DatabaseBackup;
use App\Dto\DatabaseBackupDto;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

class DatabaseBackupService
{
    private EntityManagerInterface $entityManager;
    private DatabaseBackupFactory $databaseBackupFactory;
    private ParameterBagInterface $parameterBag;

    public function __construct(
        EntityManagerInterface $entityManager,
        DatabaseBackupFactory  $databaseBackupFactory,
        ParameterBagInterface  $parameterBag
    )
    {
        $this->entityManager = $entityManager;
        $this->databaseBackupFactory = $databaseBackupFactory;
        $this->parameterBag = $parameterBag;
    }

    /**
     * @param array $params
     * @param array $returnFields
     * @return array
     */
    public function handleGetDatabaseBackup(array $returnFields): array
    {
        $result = array();

        $databaseBackups = $this->entityManager->getRepository(DatabaseBackup::class)->findAllOrderByCreatedAt();
        foreach ($databaseBackups as $databaseBackup) {
            $databaseBackupDto = new DatabaseBackupDto($databaseBackup);
            $result[] = $databaseBackupDto->toArray($returnFields);
        }

        return $result;
    }

    /**
     * @param array $data
     * @param array $returnFields
     * @return array
     * @throws \Exception
     */
    public function handlePostDatabaseBackup(array $data, array $returnFields): array
    {
        $result = array();

        $name = $data['name'];
        $desc = $data["desc"] ?? null;
        $db = $data["db"] ?? null;

        $mysqlDumpPath = $this->mysqlDump($db);
        if ($mysqlDumpPath == "") {
            throw ExceptionFactory::InternalServerException("备份数据库出错");
        }

        try {
            $databaseBackup = $this->databaseBackupFactory->create(
                $name, $desc, $db, $mysqlDumpPath
            );
            $this->entityManager->persist($databaseBackup);
            $this->entityManager->flush();
            $databaseBackupDto = new DatabaseBackupDto($databaseBackup);
            $result[] = $databaseBackupDto->toArray($returnFields);
        } catch (\Exception $exception) {
            $filesystem = new Filesystem();
            $filesystem->remove($mysqlDumpPath);
        }

        return $result;
    }

    /**
     * @param array $params
     * @param array $returnFields
     * @return array
     */
    public function handlePutDatabaseBackup(array $params, array $returnFields): array
    {
        $databaseBackup = $this->entityManager->getRepository(DatabaseBackup::class)->findOneById($params['id']);

        // example
        //if (array_key_exists('name', $params)) {
        //    $test->setName($params["name"]);
        //}

        $this->entityManager->persist($databaseBackup);
        $this->entityManager->flush();


        $databaseBackupDto = new DatabaseBackupDto($databaseBackup);
        $result[] = $databaseBackupDto->toArray($returnFields);
        return $result;
    }

    /**
     * @param array $params
     * @param array $returnFields
     * @return array
     */
    public function handleDeleteDatabaseBackup(array $params, array $returnFields): array
    {
        $result = [];

        // example
        if (array_key_exists('id', $params)) {
            $databaseBackup = $this->entityManager->getRepository(DatabaseBackup::class)->findOneById($params['id']);
            $result[] = $this->deleteDatabaseBackup($databaseBackup, $returnFields);
        }

        if (array_key_exists('ids', $params)) {
            $databaseBackups = $this->entityManager->getRepository(DatabaseBackup::class)->findByIds($params['ids']);
            foreach ($databaseBackups as $databaseBackup) {
                $result[] = $this->deleteDatabaseBackup($databaseBackup, $returnFields);
            }
        }

        return $result;
    }

    /**
     * @param DatabaseBackup $databaseBackup
     * @param array $returnFields
     * @return array
     */
    private function deleteDatabaseBackup(DatabaseBackup $databaseBackup, array $returnFields): array
    {
        $databaseBackup->setActive(0);
        $this->entityManager->persist($databaseBackup);
        $this->entityManager->flush();

        $databaseBackupDto = new DatabaseBackupDto($databaseBackup);
        return $databaseBackupDto->toArray($returnFields);
    }

    private function mysqlDump($dbName): string
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
        $currentDateTime = new DateTime();
        $currentDateTime->modify('+8 hours');
        $formattedDateTime = $currentDateTime->format('YmdHis');

        $exportPath = BASE_PATH . $this->parameterBag->get("backup_db_sql_path") . $formattedDateTime . ".sql";

        $command = "mysqldump -h " . $dbHost . " -u" . $dbUser . " -p" . $dbPwd . " " . $dbName . " > " . $exportPath;
        $process = Process::fromShellCommandline($command);
        $process->run();
        return $process->isSuccessful() ? $exportPath : "";
    }
}