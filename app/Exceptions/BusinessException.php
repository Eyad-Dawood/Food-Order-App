<?php

namespace App\Exceptions;

use Exception;

class BusinessException extends Exception
{
    protected int $statusCode;

    public function __construct(string $messageCode, int $statusCode = 400)
    {
        parent::__construct($messageCode);
        $this->statusCode = $statusCode;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
