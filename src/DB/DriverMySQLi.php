<?php

namespace Microwin7\PHPUtils\DB;

use Microwin7\PHPUtils\Utils\DebugDB;
use Microwin7\PHPUtils\Configs\MainConfig;

class DriverMySQLi
{
	private \mysqli $mysqli;
	/** @psalm-suppress PropertyNotSetInConstructor */
	private \mysqli_result|false $last_result;
	private string $sql = '';
	private string $table_prefix;
	private int|string|null $insert_id = null;
	private string $database;
	private DebugDB $debug;

	public function __construct(string $database = MainConfig::DB_NAME, string $table_prefix = '')
	{
		$this->table_prefix = $table_prefix;
		$this->database = $database;
		$this->debug = new DebugDB;
		$this->mysqli = new \mysqli(MainConfig::DB_HOST, MainConfig::DB_USER, MainConfig::DB_PASS, $database, (int)MainConfig::DB_PORT);
		if ($this->mysqli->connect_errno) $this->debug->debug("Connect error: {$this->mysqli->connect_error}");
		$this->mysqli->set_charset("utf8");
	}
	public function __destruct()
	{
		$this->close();
	}
	private function close(): void
	{
		/** @psalm-suppress RedundantCondition */
		if (!is_null($this->mysqli)) {
			$this->mysqli->close();
		}
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
	public function query(string $sql, string $param_type = "", ...$params): static
	{
		$sql = $this->sql . $sql;
		$this->sql = '';
		$this->insert_id = null;
		$this->debug->debug($param_type ?
			"[{$this->database}] Executing query: $sql with params:\n$param_type -> " . implode(', ', $params) :
			"[{$this->database}] Executing query $sql");
		try {
			$stmt = $this->mysqli->prepare($sql);
		} catch (\mysqli_sql_exception $e) {
			$this->debug->debug_error($param_type ?
				"[{$this->database}] {$e}\n$sql with params:\n$param_type -> " . implode(', ', $params) :
				"[{$this->database}] {$e}\n$sql");
			exit('MySQL query error');
		}
		if ($this->mysqli->errno) {
			$this->debug->debug_error($param_type ?
				"[{$this->database}] Statement preparing error: {$this->mysqli->error}\n$sql with params:\n$param_type -> " . implode(', ', $params) :
				"[{$this->database}] Statement preparing error: {$this->mysqli->error}\n$sql");
			exit('MySQL preparing error');
		}
		if ($param_type != "") $stmt->bind_param($param_type, ...$params);
		$stmt->execute();
		if ($stmt->errno) {
			$this->debug->debug_error($param_type ?
				"[{$this->database}] Statement execution error: {$this->mysqli->error}\n$sql with params:\n$param_type -> " . implode(', ', $params) :
				"[{$this->database}] Statement execution error: {$this->mysqli->error}\n$sql");
			exit('MySQL query error');
		}
		// Возвращается false если запрос был подготовленным выражением, например UPDATE, INSERT или DELETE
		$this->last_result = $stmt->get_result();
		$this->insert_id = @$stmt->insert_id;
		$stmt->close();
		return $this;
	}
	public function result(): \mysqli_result|false
	{
		return $this->last_result;
	}

	// Возвращаемое значение, единственное
	public function value(): mixed
	{
		return $this->column();
	}
	// Индексированный массив одной строки (Не подлежит перебору)
	public function row(): array|null|false
	{
		if ($this->last_result === false) return $this->last_result;
		return $this->last_result->fetch_row();
	}
	// Ассоциативный массив одной строки (Не подлежит перебору)
	public function assoc(): array|null|false
	{
		if ($this->last_result === false) return $this->last_result;
		return $this->last_result->fetch_assoc();
	}
	// Значение из конкретной колонки, по умолчанию первой
	public function column(int $column = 0): mixed
	{
		if ($this->last_result === false) return $this->last_result;
		return $this->last_result->fetch_column($column);
	}
	// Ассоциативный массив всех строк ответа
	public function array(): array|false
	{
		if ($this->last_result === false) return $this->last_result;
		return $this->last_result->fetch_all(MYSQLI_ASSOC);
	}
	// Индексированный и Ассоциативный массив всех строк ответа
	public function all(): array|null|false
	{
		if ($this->last_result === false) return $this->last_result;
		return $this->last_result->fetch_all(MYSQLI_BOTH);
	}
	// Объект одной строки
	/**
	 * @template T of object
	 * @param class-string<T> $class
	 * @param array $constructor_args Аргументы для конструктора передаваемого класса, для заполнения
	 * @return T|null Возвращает объект с параметрами класса как в БД и заполненными добавочными данными из аргументов констркутора класса
	 */
	public function obj($class = \stdClass::class, array $constructor_args = []): object|null
	{
		if ($this->last_result === false) return null;
		return $this->last_result->fetch_object($class, $constructor_args);
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
	 * @return array<array-key, T> Возвращает объект с параметрами класса как в БД и заполненными добавочными данными из аргументов констркутора класса
	 */
	public function objects(string $class = \stdClass::class, array $constructor_args = []): array
	{
		$array = [];
		if ($this->last_result !== false) {
			while ($obj = $this->last_result->fetch_object($class, $constructor_args)) {
				$array[] = $obj;
			}
		}
		return $array;
	}
	public function id(): int|string|null
	{
		return $this->insert_id;
	}
}
