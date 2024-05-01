<?php

declare(strict_types=1);

namespace Microwin7\PHPUtils\DB;

enum SubDBTypeEnum: string
{
    case MySQL      = 'mysql';
    case PostgreSQL = 'pgsql';

    public static function getDefault(): static
    {
        return static::MySQL;
    }
    /**
     * @return array<int|string, string>
     */
    public static function getCases(): array
    {
        return array_map('strtolower', array_combine(
            array_column(static::cases(), 'value'),
            array_column(static::cases(), 'name')
        ));
    }
    /**
     * @throws \InvalidArgumentException
     */
    public static function fromString(string $name): static
    {
        return static::tryFromString($name)
            ?? throw new \InvalidArgumentException(sprintf('Unknown Enum %s in %s', $name, static::class));
    }
    /**
     * Allow name|value
     */
    public static function tryFromString(string $name): ?static
    {
        return (false !== $found = array_search(strtolower($name), static::getCases()))
            ? static::from($found)
            : (
                (false !== array_search(strtolower($name), array_flip(static::getCases())))
                ? static::from(strtolower($name))
                : null
            );
    }
    /**
     * Default: MySQL
     * Allow name|value
     */
    public static function tryFromStringOrDefault(string $name): ?static
    {
        return static::tryFromString($name) ?? static::getDefault();
    }
}
