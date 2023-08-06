<?php

namespace App\Controller\Resource;

use App\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\DatabaseBackupService;
use App\Factory\DatabaseBackupFactory;

class DatabaseBackupController extends BaseController
{
    // Define default return fields
    const RETURN_FIELD = array(
        "name", "description", "db_name", "path", "created_at"
    );

    private DatabaseBackupService $databaseBackupService;
    private DatabaseBackupFactory $databaseBackupFactory;

    public function __construct(
        DatabaseBackupService $databaseBackupService,
        DatabaseBackupFactory $databaseBackupFactory
    )
    {
        $this->databaseBackupService = $databaseBackupService;
        $this->databaseBackupFactory = $databaseBackupFactory;
    }

    /**
     * @Route("/databaseBackup", name="获取数据库备份信息", methods={"GET"})
     * @throws \Exception
     */
    public function executeGet(Request $request): Response
    {
        $response = new Response();
        $key = $request->query->get("key");


        $params = array();
        if ($key) {
            $params["key"] = $key;
        }
        // processing
        $resultArray['data'] = $this->databaseBackupService->handleGetDatabaseBackup($params,self::RETURN_FIELD);

        $response->setContent(json_encode($resultArray));
        return $response;
    }

    /**
     * @Route("/databaseBackup", name="备份数据库", methods={"POST"})
     * @throws \Exception
     */
    public function executePost(Request $request): Response
    {
        $response = new Response();
        $params = json_decode($request->getContent(), true);
        $data = $params['data'];

        $this->validateNecessaryParameters($params, [
            'data' => self::OBJECT_TYPE
        ]);
        $this->validateNecessaryParameters($data, [
            'name' => self::STRING_TYPE,
            'db' => self::STRING_TYPE
        ]);

        // processing
        $resultArray['data'] = $this->databaseBackupService->handlePostDatabaseBackup($data, self::RETURN_FIELD);

        $response->setContent(json_encode($resultArray));
        return $response;
    }


    /**
     * @Route("/databaseBackup", name="更新数据库备份信息", methods={"PUT"})
     * @throws \Exception
     */
    public function executePut(Request $request): Response
    {
        $response = new Response();

        $params = json_decode($request->getContent(), true);
        $this->validateNecessaryParameters($params, ['data' => self::OBJECT_TYPE]);
        $data = $params['data'];

        // processing
        $resultArray['data'] = $this->databaseBackupService->handlePutDatabaseBackup($data, self::RETURN_FIELD);

        $response->setContent(json_encode($resultArray));
        return $response;
    }

    /**
     * @Route("/databaseBackup", name="删除数据库备份", methods={"DELETE"})
     * @throws \Exception
     */
    public function executeDelete(Request $request): Response
    {
        $response = new Response();

        $params = json_decode($request->getContent(), true);
        $this->validateNecessaryParameters($params, ['data' => self::OBJECT_TYPE]);
        $data = $params['data'];

        // processing
        $resultArray['data'] = $this->databaseBackupService->handleDeleteDatabaseBackup($data, self::RETURN_FIELD);

        $response->setContent(json_encode($resultArray));
        return $response;
    }

    /**
     * @Route("/databaseBackup/import", name="导入备份到数据库", methods={"POST"})
     * @throws \Exception
     */
    public function executeImportDb(Request $request): Response
    {
        $response = new Response();

        $params = json_decode($request->getContent(), true);
        $this->validateNecessaryParameters($params, ['data' => self::OBJECT_TYPE]);
        $data = $params['data'];

        $this->validateNecessaryParameters($data, ['id' => self::INT_TYPE]);

        // processing
        $resultArray['data']['handle'] = $this->databaseBackupService->handleImportDb($data);

        $response->setContent(json_encode($resultArray));
        return $response;
    }
}