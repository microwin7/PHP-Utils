<?php

namespace Microwin7\PHPUtils\Exceptions;

class TransactionException extends \Exception
{
    public function __construct()
    {
        parent::__construct("TRANSACTION_ERROR");
    }
}
