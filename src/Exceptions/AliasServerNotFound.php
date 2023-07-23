<?php

namespace Microwin7\PHPUtils\Exceptions;

class AliasServerNotFound extends \Exception
{
    function __construct() {
        parent::__construct("Запрашиваемый сервер не найден");
    }
}
