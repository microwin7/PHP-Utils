<?php

namespace Microwin7\PHPUtils\Exceptions;

class ServerNotSelected extends \Exception
{
    public function __construct()
    {
        parent::__construct("Не передан сервер для взаимодействия");
    }
}
