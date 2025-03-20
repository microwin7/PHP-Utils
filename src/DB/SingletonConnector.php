<?php

namespace Microwin7\PHPUtils\DB;

use Microwin7\PHPUtils\Main;
use Microwin7\PHPUtils\Configs\MainConfig;
use Microwin7\PHPUtils\Exceptions\ServerNotFoundException;

class SingletonConnector
{
    /** @var array<string, DriverPDOInterface> */
    private static array $database = [];

    /** @var class-string<DriverPDOInterface> */
    protected static string $driver = DriverPDO::class;

    public static function setCustomDriver(string $classDriver): void
    {
        if (is_subclass_of($classDriver, DriverPDOInterface::class)) {
            self::$driver = $classDriver;
        } else {
            throw new \RuntimeException("This $classDriver is not an implementation of " . DriverPDOInterface::class);
        }
    }
    public function __get(string $database): DriverPDOInterface
    {
        if (array_key_exists($database, self::$database)) return self::$database[$database];
        return $this->getConnect($database);
    }
    public static function get(string $database = ''): DriverPDOInterface
    {
        if (array_key_exists($database, self::$database)) return self::$database[$database];
        return self::getConnect($database);
    }
    /**
     * Singletons should not be cloneable.
     */
    protected function __clone() {}
    /**
     * Singletons should not be restorable from strings.
     * 
     * @throws \Exception
     */
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize a singleton.");
    }
    private static function getConnect(string $database): DriverPDOInterface
    {
        if (empty($database) || $database == Main::DB_NAME()) $DB_NAME = Main::DB_NAME();
        else {
            try {
                /** @psalm-suppress RedundantFunctionCall */
                $DB_NAME = strtolower(Main::DB_PREFIX_SERVERS() . Main::getServerWithoutDefault($database));
            } catch (ServerNotFoundException) {
                $DB_NAME = Main::DB_NAME_MODULE($database);
                try {
                    $DB_TABLE_PREFIX = Main::DB_TABLE_PREFIX_MODULE($database);
                } catch (\RuntimeException) {
                }
            }
        }
        if (array_key_exists($DB_NAME, self::$database)) return self::$database[$DB_NAME];
        return self::$database[$DB_NAME] = new self::$driver($DB_NAME, $DB_TABLE_PREFIX ?? '');
    }
}
