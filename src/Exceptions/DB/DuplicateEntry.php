<?php

namespace Microwin7\PHPUtils\Exceptions\DB;

class DuplicateEntry extends \Exception
{
    function __construct(string $message)
    {
        parent::__construct($message, 1062);
    }
}
