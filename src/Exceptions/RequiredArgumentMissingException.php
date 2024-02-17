<?php

namespace Microwin7\PHPUtils\Exceptions;

use function Microwin7\PHPUtils\implodeRecursive;

class RequiredArgumentMissingException extends \Exception
{
    public function __construct(string|array $arguments = 'ENCRYPTED')
    {
        if (is_array($arguments))
            parent::__construct("Отсутствуют обязательные аргументы: " . implodeRecursive(', ', $arguments));
        else parent::__construct("Отсутствует обязательный аргумент: " . $arguments);
    }
}
