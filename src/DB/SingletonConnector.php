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
        $module = [];
        if (empty($database) || $database == Main::DB_NAME()) $database = Main::DB_NAME();
        else {
            try {
                /** @psalm-suppress RedundantFunctionCall */
                $database = strtolower(Main::DB_PREFIX_SERVERS() . Main::getServerWithoutDefault($database));
            } catch (ServerNotFoundException) {
                $modules_keys_lower_case = array_change_key_case(MainConfig::MODULES);
                $key_exists = array_key_exists(strtolower($database), $modules_keys_lower_case);
                if ($key_exists === true) {
                    $module = $modules_keys_lower_case[strtolower($database)];
                    $database = $module['DB_NAME'];
                } else {
                    throw new ServerNotFoundException($database);
                    //$database = MainConfig::DB_NAME;
                }
            }
        }
        if (array_key_exists($database, self::$database)) return self::$database[$database];
        return self::$database[$database] = new self::$driver($database, $module['prefix'] ?? '');
    }
}
