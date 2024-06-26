<?php

namespace Microwin7\PHPUtils\DB;

use Microwin7\PHPUtils\Main;
use Microwin7\PHPUtils\Configs\MainConfig;
use Microwin7\PHPUtils\Exceptions\ServerNotFoundException;

class SingletonConnector
{
    /** @var array<string, DriverPDO> */
    private static array $database = [];

    public function __get(string $database): DriverPDO
    {
        if (array_key_exists($database, self::$database)) return self::$database[$database];
        return $this->getConnect($database);
    }
    public static function get(string $database = ''): DriverPDO
    {
        if (array_key_exists($database, self::$database)) return self::$database[$database];
        return self::getConnect($database);
    }
    /**
     * Singletons should not be cloneable.
     */
    protected function __clone()
    {
    }
    /**
     * Singletons should not be restorable from strings.
     * 
     * @throws \Exception
     */
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize a singleton.");
    }
    private static function getConnect(string $database): DriverPDO
    {
        $module = [];
        if (empty($database) || $database == Main::DB_NAME()) $database = Main::DB_NAME();
        else {
            try {
                /** @psalm-suppress RedundantFunctionCall */
                $database = strtolower(Main::DB_PREFIX_SERVERS() . Main::getServerWithoutDefault($database));
            } catch (ServerNotFoundException $e) {
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
        return new DriverPDO($database, $module['prefix'] ?? '');
        // /**
        //  * @var string $module['prefix']
        //  * @psalm-suppress TypeDoesNotContainType
        //  */
        // return self::$database[$database] = match (MainConfig::DB_DRIVER) {
        //     DriverTypeEnum::MySQLi => new DriverMySQLi($database, $module['prefix'] ?? ''),
        //     DriverTypeEnum::PDO => new DriverPDO($database, $module['prefix'] ?? ''),
        // };
    }
}
