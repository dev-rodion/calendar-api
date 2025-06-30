<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class UserUnauthorizedException extends UnauthorizedHttpException
{
    public function __construct(string $message = 'User is not authorized.')
    {
        parent::__construct('Bearer', $message);
    }
}