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
        if (array_key_exists($database, $this->database)) return $this->database[$database];
        return new self::$driver($database, $module['prefix'] ?? '');
    }
}
