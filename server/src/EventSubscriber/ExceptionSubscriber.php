<?php


namespace App\EventSubscriber;


use App\Factory\OperationLogFactory;
use App\Service\OperationLogService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\KernelInterface;

class ExceptionSubscriber implements EventSubscriberInterface
{
    private $environment;

    public function __construct(
        KernelInterface $kernel)
    {
        $this->environment = $kernel->getEnvironment();
    }


    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException', 800]
        ];
    }


    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

        if($this->environment !== 'dev'){
            $response = new Response();

            $code = ($exception->getCode() >= 100 && $exception->getCode() <= 800) ? $exception->getCode() : 500;
            $response->setStatusCode($code);
            $response->setContent(json_encode(
                [
                    'code' => $code,
                    'message' => $exception->getMessage(),
                ]
            ));

            $event->setResponse($response);
        }

    }
}