<?php

namespace Microwin7\PHPUtils\Exceptions;

class TextureSizeException extends \Exception
{
    public function __construct()
    {
        parent::__construct("Файл не соответствует стандартным размерам!");
    }
}
