<?php

namespace App\Controller\Functional;

use App\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\ScriptService;

class ScriptController extends BaseController
{
    private ScriptService $scriptService;

    public function __construct(
        ScriptService $scriptService
    )
    {
        $this->scriptService = $scriptService;
    }

    /**
     * @Route("/runScript", name="运行一个脚本", methods={"POST"})
     * @throws \Exception
     */
    public function executeRunScript(Request $request): Response
    {
        $params = json_decode($request->getContent(), true);
        $this->validateNecessaryParameters($params, ['data' => self::OBJECT_TYPE]);
        $data = $params['data'];

        // validate params
        // example ...
        $this->validateNecessaryParameters($data, [
            'script_id' => self::INT_TYPE,
        ]);

        $result = $this->scriptService->handleRunScript($data['script_id']);
        $response = new Response($result);
        $response->headers->set('Content-Type', 'text/javascript');
        return $response;
    }
}