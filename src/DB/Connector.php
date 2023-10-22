<?php

namespace Microwin7\PHPUtils\DB;

use Microwin7\PHPUtils\Configs\MainConfig;
use Microwin7\PHPUtils\Main;
use Microwin7\PHPUtils\Exceptions\ServerNotFoundException;

class Connector
{
    protected $database = [];

    public function __get($database): DriverPDO|DriverMySQLi
    {
        if (array_key_exists($database, $this->database)) return $this->database[$database];
        return $this->getConnect($database);
    }

    private function getConnect($database)
    {
        if (empty($database) || $database == MainConfig::DB_NAME) $database = MainConfig::DB_NAME;
        else {
            try {
                $database = strtolower(MainConfig::DB_PREFIX . Main::getServerWithoutDefault($database));
            } catch (ServerNotFoundException $e) {
                $modules_keys_lower_case = array_change_key_case(MainConfig::MODULES);
                $key_exists = array_key_exists(strtolower($database), $modules_keys_lower_case);
                if ($key_exists === true) {
                    $module = $modules_keys_lower_case[strtolower($database)];
                    $database = $module['DB_NAME'];
                } else {
                    $database = MainConfig::DB_NAME;
                }
            }
        }
        if (array_key_exists($database, $this->database)) return $this->database[$database];
        switch (strtolower(MainConfig::DB_DRIVER)) {
            case 'pdo':
                return $this->database[$database] = new DriverPDO($database, $module['prefix'] ?? '');
            default:
                return $this->database[$database] = new DriverMySQLi($database, $module['prefix'] ?? '');
        }
    }
}
