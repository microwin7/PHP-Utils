<?php

namespace Microwin7\PHPUtils\Exceptions;

class TransactionException extends \Exception
{
    function __construct() {
        parent::__construct("TRANSACTION_ERROR");
    }
}
