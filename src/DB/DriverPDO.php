<?php

namespace Microwin7\PHPUtils\DB;

use Microwin7\PHPUtils\Utils\DebugDB;
use Microwin7\PHPUtils\Configs\MainConfig;

class DriverPDO
{
    private \PDO $DBH;
    private string $DSN;
    /** @psalm-suppress PropertyNotSetInConstructor */
    private \PDOStatement $STH;
    private string $sql = '';
    private string $table_prefix;
    private int|string|null $insert_id = null;
    private string $database;
    private DebugDB $debug;

    public function __construct(string $database = MainConfig::DB_NAME, string $table_prefix = '')
    {
        $this->table_prefix = $table_prefix;
        $this->database = $database;
        $this->generateDSN();
        $this->debug = new DebugDB;
        try {
            $this->DBH = new \PDO($this->DSN, MainConfig::DB_USER, MainConfig::DB_PASS, MainConfig::DB_PDO_OPTIONS);
            $this->preConnectionExec();
        } catch (\PDOException $e) {
            $this->debug->debug_error("[{$this->database}] Connection ERROR: [CODE: " . ($e->errorInfo[1] ?? 'NULL')  . " | MESSAGE: " . ($e->errorInfo[2] ?? 'NULL') . " ]");
            exit('PDO Connection ERROR');
        }
    }
    private function generateDSN(): void
    {
        $this->DSN = MainConfig::DB_SUD_DB->value . ':host=' . MainConfig::DB_HOST . ';port=' . MainConfig::DB_PORT . ';dbname=' . $this->database;
        /** @psalm-suppress TypeDoesNotContainType */
        $this->DSN .= match (MainConfig::DB_SUD_DB) {
            SubDBTypeEnum::MySQL =>  ';charset=utf8mb4',
            SubDBTypeEnum::PostgreSQL => '',
        };
    }
    private function preConnectionExec(): void
    {
        /** @psalm-suppress TypeDoesNotContainType */
        match (MainConfig::DB_SUD_DB) {
            SubDBTypeEnum::MySQL => null, //$this->DBH->exec("set session wait_timeout = 3600; set session interactive_timeout = 3600;")
            SubDBTypeEnum::PostgreSQL => null,
        };
    }
    public function __destruct()
    {
    }
    private function table(string $table): string
    {
        return $this->table_prefix . $table . ' ';
    }
    /** @deprecated */
    public function update(string $table): static
    {
        $this->sql = "UPDATE " . $this->table($table);
        return $this;
    }
    /**
     * @param mixed &...$arr
     * 
     * @return mixed[]
     */
    private function refValues(&...$arr): array
    {
        $refs = [];
        foreach ($arr as $key => $_) {
            $refs[$key] = &$arr[$key];
        }
        return $refs;
    }

    /** @psalm-suppress MissingParamType */
    private function bind_param(string $param_type, &...$params): void
    {
        if (!empty($params)) {
            if (!empty($param_type)) {
                $arr = $this->refValues(...$params);
                $param_id = 1;
                foreach (str_split($param_type) as $var_type) {
                    match ($var_type) {
                        's' => $this->STH->bindParam($param_id, $arr[$param_id - 1], \PDO::PARAM_STR),
                        'i' => $this->STH->bindParam($param_id, $arr[$param_id - 1], \PDO::PARAM_INT),
                        'b' => $this->STH->bindParam($param_id, $arr[$param_id - 1], \PDO::PARAM_BOOL),
                        'n' => $this->STH->bindParam($param_id, $arr[$param_id - 1], \PDO::PARAM_NULL),
                        'l' => $this->STH->bindParam($param_id, $arr[$param_id - 1], \PDO::PARAM_LOB),
                        'c' => $this->STH->bindParam($param_id, $arr[$param_id - 1], \PDO::PARAM_STR_CHAR),
                        default => $this->STH->bindParam($param_id, $arr[$param_id - 1], \PDO::PARAM_STR)
                    };
                    $param_id++;
                }
            }
        }
    }

    public function query(string $sql, string $param_type = "", &...$params): static
    {
        $sql = $this->sql . $sql;
        $this->sql = '';
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

    // public function execute(){
    //     try {
    //         $this->STH->execute();
    //     } catch (\PDOException $e) {
    //         $this->debug->debug_error($param_type ?
    //             "[{$this->database}] Statement execution error: {$e}\n$sql with params:\n$param_type -> " . implode(', ', $params) :
    //             "[{$this->database}] Statement execution error: {$e}\n$sql");
    //         exit('SQL query error');
    //     }
    // }

    public function getStatementHandler(): \PDOStatement
    {
        return $this->STH;
    }
    // mysqli_result
    public function result(): array
    {
        return $this->array();
    }
    // Возвращаемое значение, единственное
    public function value(): mixed
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
    public function column(int $column = 0): mixed
    {
        $value = $this->row();
        return false === $value ? $value : $value[$column];
    }
    // Ассоциативный массив всех строк ответа
    public function array(): array
    {
        return $this->STH->fetchAll(\PDO::FETCH_ASSOC);
    }
    // Индексированный и Ассоциативный массив всех строк ответа
    public function all(): array|null|false
    {
        return $this->STH->fetchAll();
    }
    // Объект одной строки
    /**
     * @template T of object
     * @param class-string<T> $class
     * @param array $constructor_args Аргументы для конструктора передаваемого класса, для заполнения
     * @return T|null Возвращает объект с параметрами класса как в БД и заполненными добавочными данными из аргументов констркутора класса
     */
    public function obj($class = \stdClass::class, array $constructor_args = [])
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
    /**
     * @template T of object
     * @param class-string<T> $class
     * @param array $constructor_args Аргументы для конструктора передаваемого класса, для заполнения
     * @return list<T> Возвращает объект с параметрами класса как в БД и заполненными добавочными данными из аргументов констркутора класса
     */
    public function objects(string $class = \stdClass::class, array $constructor_args = []): array
    {
        /** @var list<T> */
        return $this->STH->fetchAll(\PDO::FETCH_CLASS, $class, $constructor_args);
    }
    public function id(): int|string|null
    {
        return $this->insert_id;
    }
}
