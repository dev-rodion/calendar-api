<?php

namespace App\Exception;

use RuntimeException;

class TokenGenerationException extends RuntimeException
{
    public function __construct(string $message = 'Token generation error')
    {
        parent::__construct($message);
    }
}