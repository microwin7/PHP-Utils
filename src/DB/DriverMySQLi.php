<?php

namespace Microwin7\PHPUtils\DB;

use Microwin7\PHPUtils\Utils\DebugDB;
use Microwin7\PHPUtils\Configs\MainConfig;

class DriverMySQLi
{
	private $mysqli;
	private \mysqli_result|false $last_result;
	private $sql = '';
	private $table_prefix;
	private $insert_id;
	private $database;
	private DebugDB $debug;

	public function __construct($database = MainConfig::DB_NAME, $table_prefix = '')
	{
		$this->table_prefix = $table_prefix;
		$this->database = $database;
		$this->debug = new DebugDB;
		$this->mysqli = new \mysqli(MainConfig::DB_HOST, MainConfig::DB_USER, MainConfig::DB_PASS, $database, MainConfig::DB_PORT);
		if ($this->mysqli->connect_errno) $this->debug->debug("Connect error: {$this->mysqli->connect_error}");
		$this->mysqli->set_charset("utf8");
	}
	public function __destruct()
	{
		$this->close();
	}
	private function close()
	{
		if (!is_null($this->mysqli)) {
			$this->mysqli->close();
		}
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
	public function query($sql, $param_type = "", ...$params)
	{
		$sql = $this->sql . $sql;
		$this->sql = null;
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
	public function value(): null|int|float|string|false
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
	public function column(int $column = 0): null|int|float|string|false
	{
		if ($this->last_result === false) return $this->last_result;
		return $this->last_result->fetch_column($column);
	}
	// Ассоциативный массив всех строк ответа
	public function array(): array|null|false
	{
		if ($this->last_result === null || $this->last_result === false) return $this->last_result;
		$array = [];
		foreach ($this->last_result as $item) {
			$array[] = $item;
		}
		return $array;
	}
	// Индексированный и Ассоциативный массив всех строк ответа
	public function all(): array|null|false
	{
		if ($this->last_result === null || $this->last_result === false) return $this->last_result;
		return $this->last_result->fetch_all(MYSQLI_BOTH);
	}
	// Объект одной строки
	public function obj(string $class = "stdClass", array $constructor_args = []): object|null|false
	{
		if ($this->last_result === null || $this->last_result === false) return $this->last_result;
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
	public function objects(string $class = "stdClass", array $constructor_args = []): array
	{
		$array = [];
		while ($obj = $this->last_result->fetch_object($class, $constructor_args)) {
			$array[] = $obj;
		}
		return $array;
	}
	public function id(): int|string
	{
		return $this->insert_id;
	}
}
