<?php

namespace Microwin7\PHPUtils\Exceptions;

class UserNotFoundException extends \Exception
{
    public function __construct()
    {
        parent::__construct("User Not Found", 404);
    }
}
