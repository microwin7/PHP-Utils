<?php

namespace Microwin7\PHPUtils\Exceptions;

class TextureLoaderException extends \Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
