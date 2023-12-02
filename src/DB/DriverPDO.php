<?php

namespace Microwin7\PHPUtils\DB;

use Microwin7\PHPUtils\Utils\DebugDB;
use Microwin7\PHPUtils\Configs\MainConfig;

class DriverPDO
{
    private \PDO $DBH;
    private string $DSN;
    private \PDOStatement $STH;
    private $sql = '';
    private $table_prefix;
    private $insert_id;
    private $database;
    private DebugDB $debug;

    public function __construct($database = MainConfig::DB_NAME, $table_prefix = '')
    {
        $this->table_prefix = $table_prefix;
        $this->database = $database;
        $this->generateDSN();
        $this->debug = new DebugDB;
        try {
            $this->DBH = new \PDO($this->DSN, MainConfig::DB_USER, MainConfig::DB_PASS, MainConfig::DB_PDO_OPTIONS);
            $this->preConnectionExec();
        } catch (\PDOException $e) {
            $this->debug->debug_error("[{$this->database}] Connection ERROR: [CODE: " . $e->errorInfo[1]  . " | MESSAGE: " . $e->errorInfo[2] . " ]");
            exit('PDO Connection ERROR');
        }
    }
    private function generateDSN()
    {
        $this->DSN = MainConfig::DB_SUD_DB->value . ':host=' . MainConfig::DB_HOST . ';port=' . MainConfig::DB_PORT . ';dbname=' . $this->database;
        $this->DSN .= match (MainConfig::DB_SUD_DB) {
            SubDBTypeEnum::MySQL =>  ';charset=utf8mb4',
            SubDBTypeEnum::PostgreSQL => '',
        };
    }
    private function preConnectionExec()
    {
        match (MainConfig::DB_SUD_DB) {
            SubDBTypeEnum::MySQL => null, //$this->DBH->exec("set session wait_timeout = 3600; set session interactive_timeout = 3600;")
            SubDBTypeEnum::PostgreSQL => null,
        };
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
                    match ($var_type) {
                        's' => $this->STH->bindValue($param_id, $arr[$param_id - 1], \PDO::PARAM_STR),
                        'i' => $this->STH->bindValue($param_id, $arr[$param_id - 1], \PDO::PARAM_INT),
                        'b' => $this->STH->bindValue($param_id, $arr[$param_id - 1], \PDO::PARAM_BOOL),
                        'n' => $this->STH->bindValue($param_id, $arr[$param_id - 1], \PDO::PARAM_NULL),
                        'l' => $this->STH->bindValue($param_id, $arr[$param_id - 1], \PDO::PARAM_LOB),
                        'c' => $this->STH->bindValue($param_id, $arr[$param_id - 1], \PDO::PARAM_STR_CHAR),
                        default => $this->STH->bindValue($param_id, $arr[$param_id - 1], \PDO::PARAM_STR)
                    };
                    $param_id++;
                }
            }
        }
    }
    public function query($sql, $param_type = "", ...$params): static
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
    public function getStatementHandler(): \PDOStatement
    {
        return $this->STH;
    }
    // mysqli_result
    public function result()
    {
        return $this->array();
    }
    // Возвращаемое значение, единственное
    public function value(): null|int|float|string|false
    {
        $value = $this->row();
        return false === $value ? $value : $value[0];
    }
    // Индексированный массив одной строки (Не подлежит перебору)
    public function row(): array|false
    {
        return $this->STH->fetch(\PDO::FETCH_NUM);
    }
    // Ассоциативный массив одной строки (Не подлежит перебору)
    public function assoc(): array|null|false
    {
        return $this->STH->fetch(\PDO::FETCH_ASSOC);
    }
    // Значение из конкретной колонки, по умолчанию первой
    public function column(int $column = 0): null|int|float|string|false
    {
        $value = $this->row();
        return false === $value ? $value : $value[$column];
    }
    // Ассоциативный массив всех строк ответа
    public function array(): array|null|false
    {
        return $this->STH->fetchAll(\PDO::FETCH_ASSOC);
    }
    // Индексированный и Ассоциативный массив всех строк ответа
    public function all(): array|null|false
    {
        return $this->STH->fetchAll();
    }
    // Объект одной строки
    public function obj(string $class = "stdClass", array $constructor_args = []): object|null|false
    {
        return $this->STH->fetchObject($class, $constructor_args);
    }
    /**
     * Индексированный массив объектов результата
     * Use objects()
     * @deprecated
     * @return array
     */
    public function object(): array
    {
        return $this->objects();
    }
    // Индексированный массив объектов результата
    public function objects(string $class = "stdClass", array $constructor_args = []): array
    {
        return $this->STH->fetchAll(\PDO::FETCH_CLASS, $class, $constructor_args);
    }
    public function id(): int|string
    {
        return $this->insert_id;
    }
}
