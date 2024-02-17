<?php

namespace Microwin7\PHPUtils\Exceptions;

class TextureSizeHDException extends \Exception
{
    public function __construct()
    {
        parent::__construct("Файл превышает стандартные размеры!");
    }
}
