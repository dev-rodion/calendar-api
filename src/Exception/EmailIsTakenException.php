<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class EmailIsTakenException extends ConflictHttpException
{
    public function __construct(string $message = 'Email is already taken')
    {
        parent::__construct($message);
    }
}