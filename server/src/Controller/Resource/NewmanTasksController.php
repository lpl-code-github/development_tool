<?php

namespace App\Controller\Resource;

use App\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\NewmanTaskService;
use App\Factory\NewmanTaskFactory;

class NewmanTasksController extends BaseController
{
    // Define default return fields
    const RETURN_FIELD = array(
        "name","description","html_report_path","excel_report_path","cli_output_path","created_at","updated_at","log","status"
    );

    private NewmanTaskService $newmanTaskService;
    private NewmanTaskFactory $newmanTaskFactory;

    public function __construct(
        NewmanTaskService $newmanTaskService,
        NewmanTaskFactory $newmanTaskFactory
    )
    {
        $this->newmanTaskService = $newmanTaskService;
        $this->newmanTaskFactory = $newmanTaskFactory;
    }

    /**
     * @Route("/newman_tasks", name="get newmanTasks", methods={"GET"})
     * @throws \Exception
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function executeGet(Request $request): Response
    {
        $response = new Response();

        $isUnfinished = (bool) $request->query->get("is_unfinished") ?? null;
        $id = $request->query->get("id") ?? null;
        $ids = $request->query->get("ids") ?? null;

        // validate params
        // ...

        $queryParams = array();
        if ($id) {
            $queryParams['id'] = $id;
        }
        if ($ids) {
            $queryParams['ids'] = $ids;
        }
        if($isUnfinished){
            $queryParams['is_unfinished'] = $isUnfinished;
        }

        // processing
        $resultArray['data'] = $this->newmanTaskService->handleGetNewmanTasks($queryParams, self::RETURN_FIELD);

        $response->setContent(json_encode($resultArray));
        return $response;
    }

    /**
     * @Route("/newman_tasks", name="创建测试任务", methods={"POST"})
     * @throws \Exception
     */
    public function executePost(Request $request): Response
    {
        $response = new Response();
        $resultArray = array();

        $params = json_decode($request->getContent(), true);
        $this->validateNecessaryParameters($params, ['data' => self::OBJECT_TYPE]);
        $data = $params['data'];

        $this->validateNecessaryParameters($data, [
            'name' => self::STRING_TYPE
        ]);

        // processing
        $newmanTask = $this->newmanTaskFactory->create(
            $data['name'],
            $data['description']??null
        );
        // processing
        $resultArray['data'] = $this->newmanTaskService->handlePostNewmanTasks($newmanTask, self::RETURN_FIELD);

        $response->setContent(json_encode($resultArray));
        return $response;
    }

    /**
     * @Route("/newman_tasks", name="更新Task的信息", methods={"PUT"})
     * @throws \Exception
     * 只是修改task日志 不需要记录记录接口日志
     */
    public function executePut(Request $request): Response
    {
        $response = new Response();

        $params = json_decode($request->getContent(), true);
        $this->validateNecessaryParameters($params, ['data' => self::OBJECT_TYPE]);
        $data = $params['data'];

        // validate params
        // ...

        // processing
        $resultArray['data'] = $this->newmanTaskService->handlePutNewmanTasks($data,  self::RETURN_FIELD);

        $response->setContent(json_encode($resultArray));
        return $response;
    }

    /**
     * @Route("/newman_tasks", name="remove newmanTasks", methods={"DELETE"})
     * @throws \Exception
     */
    public function executeDelete(Request $request): Response
    {
        $response = new Response();

        $params = json_decode($request->getContent(), true);
        $this->validateNecessaryParameters($params, ['data' => self::OBJECT_TYPE]);
        $data = $params['data'];

        // validate params
        // ...

        // processing
        $resultArray['data'] = $this->newmanTaskService->handleDeleteNewmanTasks($data, self::RETURN_FIELD);

        $response->setContent(json_encode($resultArray));
        return $response;
    }
}