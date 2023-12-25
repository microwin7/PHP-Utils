<?php

namespace Microwin7\PHPUtils\Exceptions;

class NeedRe_GenerateCache extends \Exception
{
    public function __construct()
    {
        parent::__construct('Необходимо повторно создать кеш');
    }
}
