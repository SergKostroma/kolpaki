<?php

namespace App\Api\Exceptions;

use Throwable;

class ExceptionRequest extends \Exception
{
    public function __construct($message, $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}