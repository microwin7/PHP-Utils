<?php

namespace Microwin7\PHPUtils\Attributes;

#[\Attribute(\Attribute::TARGET_CLASS)]
class AsArguments
{
    public function __construct(
        public string $whereSearch,
        /** @var array<string|string[]> */
        public array $required,
        /**  @var string[]|null */
        public ?array $optional = null
    ) {
    }
}
