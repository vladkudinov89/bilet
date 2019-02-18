<?php

namespace App\Exceptions;

use Exception;

class NotEnoughTicketsException extends \RuntimeException
{
    public function __construct(string $message = "Not Enough Tickets", int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }


}
