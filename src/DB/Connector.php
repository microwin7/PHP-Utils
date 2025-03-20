<?php

namespace Microwin7\PHPUtils\DB;

use Microwin7\PHPUtils\Main;
use Microwin7\PHPUtils\Configs\MainConfig;
use Microwin7\PHPUtils\Exceptions\ServerNotFoundException;

class Connector
{
    /** @var array<string, DriverPDOInterface> */
    protected array $database = [];

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
        if (array_key_exists($database, $this->database)) return $this->database[$database];
        return $this->getConnect($database);
    }
    private function getConnect(string $database): DriverPDOInterface
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
        if (array_key_exists($DB_NAME, $this->database)) return $this->database[$DB_NAME];
        return $this->database[$DB_NAME] = new self::$driver($DB_NAME, $DB_TABLE_PREFIX ?? '');
    }
}
