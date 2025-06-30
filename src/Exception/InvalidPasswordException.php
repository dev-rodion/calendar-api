<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class InvalidPasswordException extends UnauthorizedHttpException
{
    public function __construct(string $message = 'Password is incorrect')
    {
        parent::__construct('Bearer', $message);
    }
}