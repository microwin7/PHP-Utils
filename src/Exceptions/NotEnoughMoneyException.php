<?php

namespace Microwin7\PHPUtils\Exceptions;

class NotEnoughMoneyException extends \Exception
{
    function __construct() {
        parent::__construct("NOT_ENOUGHT_MONEY");
    }
}
