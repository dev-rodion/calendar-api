<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListener
{
    private array $messages = [
        400 => 'Bad request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'API endpoint not found',
        405 => 'Method not allowed',
        415 => 'Unsupported media type',
        422 => 'Unprocessable entity',
        500 => 'Internal server error',
    ];

    public function __construct(
        private readonly bool $isDebug = false
    ) {
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $statusCode = $exception instanceof HttpExceptionInterface
            ? $exception->getStatusCode()
            : 500;

        if (array_key_exists($statusCode, $this->messages) === false) {
            $message = $exception->getMessage() ?: 'An unexpected error occurred';
        } else {
            $message = $this->messages[$statusCode] ?: 'An unexpected error occurred';
        }

        $response = new JsonResponse(
            [
                'error' => [
                    'message' => $message,
                    'code' => $statusCode
                ],
            ],
            $statusCode
        );

        $event->setResponse($response);
        $event->stopPropagation();
    }
}