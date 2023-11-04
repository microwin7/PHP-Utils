<?php

namespace Microwin7\PHPUtils\Attributes;

#[\Attribute(\Attribute::TARGET_CLASS)]
class AsArguments
{
    public function __construct(
        public string $whereSearch,
        public array $required,
        public ?array $optional = null
    ) {
    }
}
