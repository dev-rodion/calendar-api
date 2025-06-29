<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ApiEndpointNotFoundException extends NotFoundHttpException
{
    public function __construct(string $message = 'API endpoint not found')
    {
        parent::__construct($message);
    }
}