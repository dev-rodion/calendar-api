<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $response = new JsonResponse(
            [
                'error' => [
                    'message' => $exception->getMessage(),
                    'code' => $exception instanceof HttpExceptionInterface && $exception->getCode() <= 0
                        ? $exception->getStatusCode()
                        : $exception->getCode(),
                ],
            ],
            $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : 500
        );

        $event->setResponse($response);
        $event->stopPropagation();
    }
}