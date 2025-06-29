<?php

namespace App\Exception;

use RuntimeException;

class UserUpdateException extends RuntimeException
{
    public function __construct(string $message = 'Failed to update user.')
    {
        parent::__construct($message);
    }
}