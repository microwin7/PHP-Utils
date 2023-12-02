<?php

namespace Microwin7\PHPUtils\Attributes;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class RegexArguments
{
    public function __construct(
        public string $argument,
        public string $regexp
    ) {
    }
}
