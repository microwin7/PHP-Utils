<?php

namespace Microwin7\PHPUtils\Exceptions;

define('BEARER_TOKEN_INCORRECT', 'BearerToken Incorrect');

class ValidateBearerTokenException extends \Exception
{
    function __construct()
    {
        parent::__construct(BEARER_TOKEN_INCORRECT);
    }
}
