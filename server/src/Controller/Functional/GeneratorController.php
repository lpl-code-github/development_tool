<?php

namespace App\Controller\Functional;

use App\Service\GeneratorService;
use App\Service\SystemService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GeneratorController extends AbstractController
{
    private GeneratorService $generatorService;
    public function __construct(
        GeneratorService $generatorService
    )
    {
        $this->generatorService = $generatorService;
    }

    /**
     * @Route("/generatePostmanTest", name="生成POSTMAN测试", methods={"POST"})
     */
    public function generatePostmanTest(Request $request): Response
    {
        $params = json_decode($request->getContent(),true);
        $result = $this->generatorService->generatorPostmanTest($params);

        $response = new Response($result);
        $response->headers->set('Content-Type', 'text/javascript');

        return $response;
    }
}