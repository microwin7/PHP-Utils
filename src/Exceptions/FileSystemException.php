<?php

namespace Microwin7\PHPUtils\Exceptions;

class FileSystemException extends \Exception
{
    function __construct($input)
    {
        parent::__construct($input);
    }
}
