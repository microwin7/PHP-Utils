<?php

declare(strict_types=1);

namespace Microwin7\PHPUtils\Request;

interface RequestParamsInterface
{
    public static function fromRequest(): static;
    public function __toString(): string;
}
