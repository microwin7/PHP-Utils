<?php

namespace Microwin7\PHPUtils\Exceptions;

class DBException extends \Exception
{
    function __construct(string $message)
    {
        parent::__construct($message);
    }
}
