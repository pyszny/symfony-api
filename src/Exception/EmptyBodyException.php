<?php

namespace App\Exception;

class EmptyBodyException extends \Exception
{
    public function __construct(
        string $message = "",
        int $code = 0,
        Throwable $previous = null
    )
    {
        parent::__construct('The body od the POST/PUT method cannot be empty.', $code, $previous);
    }
}