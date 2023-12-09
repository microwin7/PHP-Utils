<?php

declare(strict_types=1);

namespace Microwin7\PHPUtils\Contracts\Enum;

interface EnumInterface
{
    public static function getCases(): array;
    public static function fromString(string $name): static;
    public static function tryFromString(string $name): ?static;
    public static function getNameVariable(): string;
}
