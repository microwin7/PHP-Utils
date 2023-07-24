<?php

namespace Microwin7\PHPUtils\Exceptions;

class NumberFormatException extends \Exception
{
    function __construct($input)
    {
        parent::__construct("For input string: \"$input\"");
    }
}
