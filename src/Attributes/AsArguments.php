<?php

namespace Microwin7\PHPUtils\Attributes;

use Microwin7\PHPUtils\Contracts\Component\Enum\HTTP;

#[\Attribute(\Attribute::TARGET_CLASS)]
class AsArguments
{
    public function __construct(
        public HTTP $whereSearch,
        /** @var array<string|string[]> */
        public array $required,
        /**  @var string[]|null */
        public ?array $optional = null
    ) {
    }
}
