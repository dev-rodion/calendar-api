<?php

namespace App\Exception;

use DomainException;

class SamePasswordException extends DomainException
{
    public function __construct(string $message = 'New password must be different from current password')
    {
        parent::__construct($message);
    }
}