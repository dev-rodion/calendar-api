<?php

namespace App\Exception;

class PasswordChangeException extends \RuntimeException
{
    public function __construct(string $message = 'Failed to change password', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}