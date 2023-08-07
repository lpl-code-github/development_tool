<?php

namespace App\Controller\Functional;

use App\Controller\BaseController;
use App\Service\NewmanService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class NewmanController extends BaseController
{
    private NewmanService $newmanService;

    public function __construct(
        NewmanService $newmanService
    )
    {
        $this->newmanService = $newmanService;
    }

    /**
     * @Route("/runNewman", name="进行newman测试", methods={"POST"})
     * @throws \Exception
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function executeRunNewman(Request $request): JsonResponse
    {
        $response = new Response();
        $resultArray = array();

        $params = json_decode($request->getContent(), true);
        $this->validateNecessaryParameters($params, ['data' => self::OBJECT_TYPE]);
        $data = $params['data'];

        $this->validateNecessaryParameters($data, [
            'task_id' => self::INT_TYPE
        ]);

        $resultArray['data']['handle'] = $this->newmanService->handleRunNewman($data['task_id']);
        return new JsonResponse($resultArray);
    }
}