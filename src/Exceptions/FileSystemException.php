<?php

namespace Microwin7\PHPUtils\Exceptions;

class FileSystemException extends \Exception
{
    function __construct(string $input)
    {
        parent::__construct($input);
    }
}
