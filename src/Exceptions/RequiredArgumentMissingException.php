<?php

namespace Microwin7\PHPUtils\Exceptions;

class RequiredArgumentMissingException extends \Exception
{
    function __construct(string|array $arguments = 'ENCRYPTED')
    {
        if (is_array($arguments))
            parent::__construct("Отсутствуют обязательные аргументы: " . implode(', ', $arguments));
        else parent::__construct("Отсутствует обязательный аргумент: " . $arguments);
    }
}
