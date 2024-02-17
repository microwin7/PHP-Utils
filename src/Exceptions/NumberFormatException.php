<?php

namespace Microwin7\PHPUtils\Exceptions;

class NumberFormatException extends \Exception
{
    public function __construct(string $message)
    {
        parent::__construct("For input string: \"$message\"");
    }
}
