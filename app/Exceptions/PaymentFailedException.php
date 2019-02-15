<?php

namespace App\Exceptions;

class PaymentFailedException extends \RuntimeException
{
    public function __construct(string $message = "token is wrong", int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
