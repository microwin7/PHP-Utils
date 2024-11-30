<?php

namespace Microwin7\PHPUtils\Exceptions;

class RegexArgumentsFailedException extends \Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
    public static function pattern(string $argument, string $regexp, mixed $argument_given): self
    {
        return new self(sprintf(
            'Field "%s" should be valid with pattern: [%s], "%s" given',
            $argument,
            $regexp,
            (string)$argument_given
        ));
    }
}
