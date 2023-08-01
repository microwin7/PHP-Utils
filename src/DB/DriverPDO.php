<?php

namespace Microwin7\PHPUtils\DB;

use Microwin7\PHPUtils\Configs\MainConfig;
use Microwin7\PHPUtils\Utils\Debug;

class DriverPDO
{
    private \PDO $DBH;
    private string $DSN;
    private \PDOStatement $STH;
    private $sql = '';
    private $table_prefix;
    private $insert_id;
    private $database;
    private Debug $debug;

    public function __construct($database = MainConfig::DB_NAME, $table_prefix = '')
    {
        $this->table_prefix = $table_prefix;
        $this->database = $database;
        $this->debug = new Debug;
        $this->generateDSN();
        try {
            $this->DBH = new \PDO($this->DSN, MainConfig::DB_USER, MainConfig::DB_PASS, [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION, \PDO::ATTR_PERSISTENT => true]);
            $this->preConnectionExec();
        } catch (\PDOException $e) {
            $this->debug->debug_error("[{$this->database}] Connection ERROR: [CODE: " . $e->errorInfo[1]  . " | MESSAGE: " . $e->errorInfo[2] . " ]");
            exit('PDO Connection ERROR');
        }
    }
    private function generateDSN()
    {
        switch (strtolower(MainConfig::DB_SUD_DB)) {
            case 'pgsql':
                $this->DSN = "pgsql:host=" . MainConfig::DB_HOST . ";port=" . MainConfig::DB_PORT . ";dbname=" . $this->database;
                break;
            default:
                $this->DSN = "mysql:host=" . MainConfig::DB_HOST . ";port=" . MainConfig::DB_PORT . ";dbname=" . $this->database . ";charset=utf8mb4";
        }
    }
    private function preConnectionExec()
    {
        switch (strtolower(MainConfig::DB_SUD_DB)) {
            case 'pgsql':
                break;
            case 'mysql':
                // $this->DBH->exec("set session wait_timeout = 3600; set session interactive_timeout = 3600;");
                break;
            default:
                break;
        }
    }
    public function __destruct()
    {
    }
    private function table($table)
    {
        return $this->table_prefix . $table . ' ';
    }
    public function update($table)
    {
        $this->sql = "UPDATE " . $this->table($table);
        return $this;
    }
    private function refValues(...$arr)
    {
        $refs = [];
        foreach ($arr as $key => $value) {
            $refs[$key] = &$arr[$key];
        }
        return $refs;
    }

    private function bind_param($param_type, ...$params)
    {
        if (!empty($params)) {
            if (!empty($param_type) && is_string($param_type)) {
                $arr = $this->refValues(...$params);
                $param_id = 1;
                foreach (str_split($param_type) as $var_type) {
                    switch ($var_type) {
                        case 's':
                            $this->STH->bindValue($param_id, $arr[$param_id - 1], \PDO::PARAM_STR);
                            break;
                        case 'i':
                            $this->STH->bindValue($param_id, $arr[$param_id - 1], \PDO::PARAM_INT);
                            break;
                        case 'b':
                            $this->STH->bindValue($param_id, $arr[$param_id - 1], \PDO::PARAM_BOOL);
                            break;
                        case 'n':
                            $this->STH->bindValue($param_id, $arr[$param_id - 1], \PDO::PARAM_NULL);
                            break;
                        case 'l':
                            $this->STH->bindValue($param_id, $arr[$param_id - 1], \PDO::PARAM_LOB);
                            break;
                        case 'c':
                            $this->STH->bindValue($param_id, $arr[$param_id - 1], \PDO::PARAM_STR_CHAR);
                            break;
                        default:
                            $var_type = \PDO::PARAM_STR;
                    }
                    $param_id++;
                }
            }
        }
    }
    public function query($sql, $param_type = "", ...$params)
    {
        $sql = $this->sql . $sql;
        $this->sql = null;
        $this->insert_id = null;
        $this->debug->debug($param_type ?
            "[{$this->database}] Executing query: $sql with params:\n$param_type -> " . implode(', ', $params) :
            "[{$this->database}] Executing query $sql");
        try {
            $this->STH = $this->DBH->prepare($sql);
        } catch (\PDOException $e) {
            $this->debug->debug_error($param_type ?
                "[{$this->database}] Statement preparing error: {$e}\n$sql with params:\n$param_type -> " . implode(', ', $params) :
                "[{$this->database}] Statement preparing error: {$e}\n$sql");
            exit('SQL preparing error');
        }
        $this->bind_param($param_type, ...$params);
        try {
            $this->STH->execute();
        } catch (\PDOException $e) {
            $this->debug->debug_error($param_type ?
                "[{$this->database}] Statement execution error: {$e}\n$sql with params:\n$param_type -> " . implode(', ', $params) :
                "[{$this->database}] Statement execution error: {$e}\n$sql");
            exit('SQL query error');
        }
        try {
            $this->insert_id = $this->DBH->lastInsertId();
        } catch (\PDOException $ignored) {
            // Ошибка только с postgresql, предположительно, необходимо вызывать NEXTVAL('?') до LASTVAL()
            //Fatal error: Uncaught PDOException: SQLSTATE[55000]: Object not in prerequisite state: 7 ERROR:
            //lastval is not yet defined in this session in Utils/DBDriverPDO.php:136
        }
        return $this;
    }
    public function getStatementHandler()
    {
        return $this->STH;
    }
    // mysqli_result
    public function result()
    {
        return $this->array();
    }
    // null|false|mixed Первое значение в массиве
    public function value()
    {
        $value = $this->row();
        if (!empty($value)) return $value[0];
        return $value;
    }
    // null|false|array Индексированный массив одной строки (Не подлежит перебору)
    public function row()
    {
        return $this->STH->fetch(\PDO::FETCH_NUM);
    }
    // null|false|array Ассоциативный массив одной строки (Не подлежит перебору)
    public function assoc()
    {
        return $this->STH->fetch(\PDO::FETCH_ASSOC);
    }
    // null|false|array Ассоциативный массив всех строк ответа
    public function array()
    {
        return $this->STH->fetchAll(\PDO::FETCH_ASSOC);
    }
    // [] Индексированный массив объектов результата
    public function object()
    {
        return $this->STH->fetchAll(\PDO::FETCH_OBJ);
    }
    public function id()
    {
        return $this->insert_id;
    }
}
