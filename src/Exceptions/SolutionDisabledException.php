<?php

namespace Microwin7\PHPUtils\Exceptions;

class SolutionDisabledException extends \Exception
{
    public function __construct()
    {
        parent::__construct("Функция отключена или решение не может быть вызвано");
    }
}
