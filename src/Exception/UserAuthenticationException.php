<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserAuthenticationException extends NotFoundHttpException
{
    public function __construct(string $message = 'User authentication failed.')
    {
        parent::__construct($message);
    }
}