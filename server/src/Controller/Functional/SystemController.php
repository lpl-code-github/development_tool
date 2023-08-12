<?php

namespace App\Controller\Functional;

use App\Service\SystemService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SystemController extends AbstractController
{
    private SystemService $systemService;
    public function __construct(
        SystemService $systemService
    )
    {
        $this->systemService = $systemService;
    }

    /**
     * @Route("/getSystemStatus", name="获取系统状态", methods={"GET"})
     */
    public function getSystemStatus(): JsonResponse
    {
        return new JsonResponse([
            'cpu_usage' => $this->systemService->getSystemInfo("cpu"),
            'memory_usage' => $this->systemService->getSystemInfo("memory"),
        ]);
    }

    /**
     * @Route("/getPs", name="获取系统排名前5的进程", methods={"GET"})
     */
    public function getPs(): Response
    {
        $top5Ps = $this->systemService->getSystemInfo("top5_ps");
        $response = new Response($top5Ps);
        $response->headers->set('Content-Type', 'text/javascript');
        return $response;
    }
}