<?php


namespace App\EventSubscriber;


use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\KernelInterface;

class ExceptionSubscriber implements EventSubscriberInterface
{

    private $environment;

    public function __construct(
        KernelInterface $kernel
    )
    {
        $this->environment = $kernel->getEnvironment();
    }

    public static function getSubscribedEvents()
    {

        return [
            KernelEvents::EXCEPTION => ['onKernelException', 910]
        ];
    }


    public function onKernelException(ExceptionEvent $event)
    {
        // record error log
        $exception = $event->getThrowable();

        //only dev mode show error page
        // Whether to use symfony error page
        // environment is prod, it should not
        if($this->environment !== 'dev'){
            $response = new Response();
            if($exception->getCode() >= 100 && $exception->getCode() <= 800){
                $response->setStatusCode($exception->getCode());
                $messageArray = [
                    'code' => $exception->getCode(),
                    'message' => $exception->getMessage(),
                ];
                $response->setContent(json_encode($messageArray));
            }else{
                $messageArray = [
                    'code' => 500
                ];
                $response->setContent(json_encode($messageArray));

            }
            $event->setResponse($response);
        }

    }
}