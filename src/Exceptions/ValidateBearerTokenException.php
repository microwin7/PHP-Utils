<?php

namespace Microwin7\PHPUtils\Exceptions;

class ValidateBearerTokenException extends \Exception
{
    function __construct()
    {
        parent::__construct('Incorrect BearerToken');
    }
}
