<?php

namespace Microwin7\PHPUtils\Exceptions;

class RconConnectException extends \Exception
{
    public function __construct()
    {
        parent::__construct("Соединение с сервером не установлено. Операция не выполнена");
    }
}
