<?php

namespace Microwin7\PHPUtils\Exceptions;

class ServerNotFoundException extends \Exception
{
    public function __construct(string $serverName = '')
    {
        parent::__construct("Запрашиваемый сервер не найден: \"$serverName\"");
    }
}
