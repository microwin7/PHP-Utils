<?php

declare(strict_types=1);

namespace Microwin7\PHPUtils\Contracts\Enum;

trait EnumTrait
{
    /**
     * @return array<int|string, string>
     */
    public static function getCases(): array
    {
        return array_combine(
            array_column(static::cases(), 'value'),
            array_column(static::cases(), 'name')
        );
    }
    /**
     * @throws \InvalidArgumentException
     */
    public static function fromString(string $name): static
    {
        return static::tryFromString($name)
            ?? throw new \InvalidArgumentException(sprintf('Unknown %s: %s', static::getNameVariable(), $name));
    }
    public static function tryFromString(string $name): ?static
    {
        return (false !== $found = array_search(strtoupper($name), static::getCases(), true))
            ? static::from($found)
            : null;
    }
    public function toString(): string
    {
        return static::getCases()[$this->value];
    }
    public function jsonSerialize(): mixed
    {
        return $this->toString();
    }
}
