<?php

namespace Microwin7\PHPUtils\Exceptions;

class NoSuchRequestMethodException extends \Exception
{
    public function __construct()
    {
        parent::__construct("Таких запросов к API не предусмотрено");
    }
}
