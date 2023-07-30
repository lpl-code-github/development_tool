<?php

namespace App\Controller\Functional;

use App\Service\SystemService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @Route("/getSystemStatus", name="get system status", methods={"GET"})
     */
    public function getSystemStats()
    {
        return new JsonResponse([
            'cpu_usage' => $this->systemService->getSystemUsage("cpu"),
            'memory_usage' => $this->systemService->getSystemUsage("memory")
        ]);
    }
}