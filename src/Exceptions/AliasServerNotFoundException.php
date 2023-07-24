<?php

namespace Microwin7\PHPUtils\Exceptions;

class AliasServerNotFoundException extends \Exception
{
    function __construct() {
        parent::__construct("Запрашиваемый сервер не найден");
    }
}
