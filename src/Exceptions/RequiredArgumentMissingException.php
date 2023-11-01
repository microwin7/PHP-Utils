<?php

namespace Microwin7\PHPUtils\Exceptions;

class RequiredArgumentMissingException extends \Exception
{
    function __construct(string|array $arguments = 'ENCRYPTED')
    {
        parent::__construct("Отсутствуют обязательные аргументы: " . is_array($arguments) ? implode(', ', $arguments) : $arguments);
    }
}
