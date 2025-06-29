<?php 

namespace App\Exception;

use RuntimeException;

class UserRegistrationException extends RuntimeException
{
    public function __construct(string $message = 'User registration error')
    {
        parent::__construct($message);
    }
}
