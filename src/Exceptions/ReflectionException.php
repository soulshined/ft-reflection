<?php

namespace FT\Reflection\Exceptions;

use RuntimeException;

class ReflectionException extends RuntimeException {

    public function __construct(string $message, int $code = 0)
    {
        parent::__construct($message, $code);
    }

}