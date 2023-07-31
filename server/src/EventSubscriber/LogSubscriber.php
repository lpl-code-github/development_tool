<?php

namespace App\EventSubscriber;

use App\Factory\OperationLogFactory;
use App\Service\OperationLogService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LogSubscriber implements EventSubscriberInterface
{
    private OperationLogFactory $operationLogFactory;
    private OperationLogService $operationLogService;
    private $errorMessage = null;

    public function __construct(OperationLogFactory $operationLogFactory, OperationLogService $operationLogService)
    {
        $this->operationLogFactory = $operationLogFactory;
        $this->operationLogService = $operationLogService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => ['onKernelResponse', 700],
            KernelEvents::EXCEPTION => ['onKernelException', 900] // 这里的优先级高，先走LogSubscriber的onKernelException，再走ExceptionSubscriber的
        ];
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        $request = $event->getRequest();
        $response = $event->getResponse();
        $method = $request->getMethod();
        // Only process POST, DELETE, PUT, PATCH requests
        if (in_array($method, ['POST', 'DELETE', 'PUT', 'PATCH'])) {
            // 创建日志
            $operationLog = $this->operationLogFactory->create($request, $response, $this->errorMessage);
            // 保存日志
            $this->operationLogService->saveOperationLog($operationLog);
        }

    }
    public function onKernelException(ExceptionEvent $event)
    {
        // record error log
        $exception = $event->getThrowable();
        $this->errorMessage = $exception->getMessage();
    }
}
