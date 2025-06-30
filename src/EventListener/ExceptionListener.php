<?php

namespace App\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

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

    private array $exceptionTypeMapping = [
        NotFoundHttpException::class => 'ApiEndpointNotFoundException',
        MethodNotAllowedHttpException::class => 'MethodNotAllowedException',
    ];

    public function __construct(
        private readonly bool $isDebug = false,
        private readonly ?LoggerInterface $logger = null
    ) {
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof AuthenticationException) {
            $statusCode = 401;
        } elseif ($exception instanceof AccessDeniedException) {
            $statusCode = 403;
        } else {
            $statusCode = $exception instanceof HttpExceptionInterface
                ? $exception->getStatusCode()
                : 500;
        }

        if ($this->logger) {
            $context = [
                'exception' => $exception,
                'status_code' => $statusCode,
            ];

            if ($statusCode >= 500) {
                $this->logger->error($exception->getMessage(), $context);
            } else {
                $this->logger->warning($exception->getMessage(), $context);
            }
        }

        $response = new JsonResponse(
            $this->buildErrorResponse($exception, $statusCode),
            $statusCode
        );

        $event->setResponse($response);
        $event->stopPropagation();
    }

    private function buildErrorResponse(\Throwable $exception, int $statusCode): array
    {
        $errorData = [
            'error' => [
                'message' => $this->getErrorMessage($exception, $statusCode),
                'code' => $statusCode,
                'type' => $this->getErrorType($exception),
            ],
        ];

        if ($this->isDebug) {
            $errorData['debug'] = [
                'original_message' => $exception->getMessage(),
                'original_type' => get_class($exception),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $this->formatTrace($exception->getTrace()),
            ];

            if ($exception->getPrevious()) {
                $errorData['debug']['previous'] = [
                    'message' => $exception->getPrevious()->getMessage(),
                    'type' => get_class($exception->getPrevious()),
                ];
            }
        }

        return $errorData;
    }

    private function getErrorMessage(\Throwable $exception, int $statusCode): string
    {
        if (isset($this->messages[$statusCode])) {
            return $this->messages[$statusCode];
        }

        return $exception->getMessage() ?: 'An unexpected error occurred';
    }

    private function formatTrace(array $trace): array
    {
        $formattedTrace = [];
        $maxTraceDepth = 10;

        foreach (array_slice($trace, 0, $maxTraceDepth) as $index => $traceItem) {
            $formattedTrace[$index] = [
                'file' => $traceItem['file'] ?? 'unknown',
                'line' => $traceItem['line'] ?? 'unknown',
                'function' => ($traceItem['class'] ?? '') . ($traceItem['type'] ?? '') . ($traceItem['function'] ?? 'unknown'),
            ];
        }

        return $formattedTrace;
    }

    private function getErrorType(\Throwable $exception): string
    {
        $exceptionClass = get_class($exception);

        if (isset($this->exceptionTypeMapping[$exceptionClass])) {
            return $this->exceptionTypeMapping[$exceptionClass];
        }

        if (str_starts_with($exceptionClass, 'App\\Exception\\')) {
            return basename(str_replace('\\', '/', $exceptionClass));
        }

        return 'ApplicationException';
    }
}