<?php

namespace App\Exception;

use RuntimeException;

class UserDeletionException extends RuntimeException
{
    public function __construct(string $message = 'Failed to delete user.')
    {
        parent::__construct($message);
    }
}