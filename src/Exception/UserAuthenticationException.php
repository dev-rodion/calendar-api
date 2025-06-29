<?php

namespace App\Exception;

use RuntimeException;

class UserAuthenticationException extends RuntimeException
{
    public function __construct(string $message = 'User authentication failed.')
    {
        parent::__construct($message);
    }
}