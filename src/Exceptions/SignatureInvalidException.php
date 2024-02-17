<?php

namespace Microwin7\PHPUtils\Exceptions;

class SignatureInvalidException extends \Exception
{
    public function __construct()
    {
        parent::__construct("Signature verification failed");
    }
}
