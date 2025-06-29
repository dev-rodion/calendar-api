<?php

namespace App\Exception;

use DomainException;

class InvalidCurrentPasswordException extends DomainException
{
    public function __construct(string $message = 'Current password is incorrect')
    {
        parent::__construct($message);
    }
}