<?php

declare(strict_types=1);

namespace Microwin7\PHPUtils\Contracts\Enum;

interface EnumRequestInterface
{
    public static function getDefault(): static;
    public static function getNameRequestVariable(): string;
    public static function getNameVariable(): string;
}
