<?php

namespace App\Service;

use App\Dto\DatabaseBackupDto;
use App\Entity\DatabaseBackup;
use App\Factory\DatabaseBackupFactory;
use App\Factory\ExceptionFactory;
use App\Service\DB\DBService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;


class DatabaseBackupService
{
    private EntityManagerInterface $entityManager;
    private DatabaseBackupFactory $databaseBackupFactory;
    private ParameterBagInterface $parameterBag;
    private DBService $DBService;

    public function __construct(
        EntityManagerInterface $entityManager,
        DatabaseBackupFactory  $databaseBackupFactory,
        ParameterBagInterface  $parameterBag,
        DBService $DBService
    )
    {
        $this->entityManager = $entityManager;
        $this->databaseBackupFactory = $databaseBackupFactory;
        $this->parameterBag = $parameterBag;
        $this->DBService = $DBService;
    }

    /**
     * @param array $params
     * @param array $returnFields
     * @return array
     */
    public function handleGetDatabaseBackup(array $params,array $returnFields): array
    {
        $result = array();

        if (count($params) == 0){
            $databaseBackups = $this->entityManager->getRepository(DatabaseBackup::class)->findAllOrderByCreatedAt();
            foreach ($databaseBackups as $databaseBackup) {
                $databaseBackupDto = new DatabaseBackupDto($databaseBackup);
                $result[] = $databaseBackupDto->toArray($returnFields);
            }
        }
        if (array_key_exists('key',$params)){
            $databaseBackups = $this->entityManager->getRepository(DatabaseBackup::class)->findLikeNameOrDesc($params['key']);
            foreach ($databaseBackups as $databaseBackup) {
                $databaseBackupDto = new DatabaseBackupDto($databaseBackup);
                $result[] = $databaseBackupDto->toArray($returnFields);
            }
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

        list($dbHost, $dbUser, $dbPwd) = $this->DBService->getDBInfo();
        $currentDateTime = new DateTime();
        $currentDateTime->modify('+8 hours');
        $formattedDateTime = $currentDateTime->format('YmdHis');

        $exportPath = BASE_PATH . $this->parameterBag->get("backup_db_sql_path") . $formattedDateTime . ".sql";
        $mysqlDump = $this->DBService->mysqlDump($dbHost, $dbUser, $dbPwd, $db,$exportPath);
        if (!$mysqlDump) {
            throw ExceptionFactory::InternalServerException("备份数据库出错");
        }

        try {
            $databaseBackup = $this->databaseBackupFactory->create(
                $name, $desc, $db, $exportPath
            );
            $this->entityManager->persist($databaseBackup);
            $this->entityManager->flush();
            $databaseBackupDto = new DatabaseBackupDto($databaseBackup);
            $result[] = $databaseBackupDto->toArray($returnFields);
        } catch (\Exception $exception) {
            $filesystem = new Filesystem();
            $filesystem->remove($exportPath);
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

        if (array_key_exists('name', $params)) {
            $databaseBackup->setName($params["name"]);
        }
        if (array_key_exists('description', $params)) {
            $databaseBackup->setDescription($params["description"]);
        }

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
     * @throws \Exception
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
     * @throws \Exception
     */
    private function deleteDatabaseBackup(DatabaseBackup $databaseBackup, array $returnFields): array
    {
        $this->entityManager->beginTransaction();
        try {
            $filesystem = new Filesystem();
            $path = $databaseBackup->getPath();

            $databaseBackupDto = new DatabaseBackupDto($databaseBackup);
            $this->entityManager->getRepository(DatabaseBackup::class)->remove($databaseBackup, true);

            $filesystem->remove($path);
            $this->entityManager->commit();
            return $databaseBackupDto->toArray($returnFields);
        }catch (\Exception $exception){
            $this->entityManager->rollback();
            throw ExceptionFactory::InternalServerException("删除失败：".$exception->getMessage());
        }
    }

    /**
     * 导入备份文件
     * @throws \Exception
     */
    public function handleImportDb($data): bool
    {
        // 获取数据库信息
        list($dbHost, $dbUser, $dbPwd) = $this->DBService->getDBInfo();

        // 获取备份文件信息
        $databaseBackup = $this->entityManager->getRepository(DatabaseBackup::class)->findOneById($data['id']);
        $path = $databaseBackup->getPath(); // 备份文件
        $dbName = $databaseBackup->getDbName();

        // 判断备份文件是否存在
        $filesystem = new Filesystem();
        if (!$filesystem->exists($path)){
            throw ExceptionFactory::InternalServerException("备份文件可能已经丢失");
        }

        // 操作之前先临时备份数据库
        $currentDateTime = new DateTime();
        $currentDateTime->modify('+8 hours');
        $formattedDateTime = $currentDateTime->format('YmdHis');
        $exportPath = BASE_PATH . $this->parameterBag->get("tmp_backup_db_sql_path") . $formattedDateTime . ".sql"; // 临时备份文件
        $mysqlDump = $this->DBService->mysqlDump($dbHost, $dbUser, $dbPwd, $dbName, $exportPath);
        if (!$mysqlDump) {
            throw ExceptionFactory::InternalServerException("操作前需要临时备份数据库，但是出错了");
        }

        // 如果存在数据库删除数据库
        $deleteDb = $this->DBService->deleteDb($dbHost, $dbUser, $dbPwd, $dbName);
        if (!$deleteDb){
            throw ExceptionFactory::InternalServerException("删除数据库".$dbName."失败，"."当前数据库如果已经损坏，已经保留本操作前的SQL: ".$exportPath);
        }

        // 创建数据库
        $createDb = $this->DBService->createDb($dbHost, $dbUser, $dbPwd, $dbName);
        if (!$createDb){
            throw ExceptionFactory::InternalServerException("创建数据库".$dbName."失败，"."当前数据库如果已经损坏，已经保留本操作前的SQL: ".$exportPath);
        }

        // 导入数据
        $importDb = $this->DBService->importDb($dbHost, $dbUser, $dbPwd, $dbName, $path);
        if (!$importDb){
            throw ExceptionFactory::InternalServerException("导入数据库".$dbName."失败，"."当前数据库如果已经损坏，已经保留本操作前的SQL: ".$exportPath);
        }

        // 删除临时文件
        if ($filesystem->exists($exportPath)){
            $filesystem->remove($exportPath);
        }
        return true;
    }


}