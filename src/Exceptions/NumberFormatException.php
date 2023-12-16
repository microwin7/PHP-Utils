<?php

namespace Microwin7\PHPUtils\Exceptions;

class NumberFormatException extends \Exception
{
    function __construct(string $input)
    {
        parent::__construct("For input string: \"$input\"");
    }
}
