<?php

namespace Microwin7\PHPUtils\Attributes;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class RegexArguments
{
    public function __construct(
        public string $argument,
        public string $regexp,
        public ?string $messageCallback = null
    ) {
    }
}
