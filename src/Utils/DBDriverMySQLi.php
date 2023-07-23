<?php

namespace Microwin7\PHPUtils\Utils;

use \Microwin7\PHPUtils\Configs\Main;

class DBDriverMySQLi
{
	private $mysqli;
	private $last;
	private $sql = '';
	private $table_prefix;
	private $insert_id;
	private $database;
	private DBDebug $debug;

	public function __construct($database = Main::DB_NAME, $table_prefix = '')
	{
		$this->table_prefix = $table_prefix;
		$this->database = $database;
		$this->debug = new DBDebug;
		$this->mysqli = new \mysqli(Main::DB_HOST, Main::DB_USER, Main::DB_PASS, $database, Main::DB_PORT);
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
		$this->last = $stmt->get_result();
		$this->insert_id = @$stmt->insert_id;
		$stmt->close();
		return $this;
	}
	// mysqli_result
	public function result()
	{
		return $this->last;
	}
	// null|false|mixed Первое значение в массиве
	public function value()
	{
		if ($this->last === null || $this->last === false) return $this->last;
		$array = $this->last->fetch_row();
		if (!empty($array)) return $array[0];
		return $array;
	}
	// null|false|array Индексированный массив одной строки (Не подлежит перебору)
	public function row()
	{
		if ($this->last === null || $this->last === false) return $this->last;
		return $this->last->fetch_row();
	}
	// null|false|array Ассоциативный массив одной строки (Не подлежит перебору)
	public function assoc()
	{
		if ($this->last === null || $this->last === false) return $this->last;
		return $this->last->fetch_assoc();
	}
	// null|false|array Ассоциативный массив всех строк ответа
	public function array()
	{
		if ($this->last === null || $this->last === false) return $this->last;
		$array = [];
		foreach ($this->last as $item) {
			$array[] = $item;
		}
		return $array;
	}
	// null|false|array Индексированный массив одной строки (Не подлежит перебору)
	public function all()
	{
		if ($this->last === null || $this->last === false) return $this->last;
		return $this->last->fetch_all();
	}
	// [] Индексированный массив объектов результата
	public function object()
	{
		$array = [];
		while ($obj = $this->last->fetch_object()) {
			$array[] = $obj;
		}
		return $array;
	}
	public function id()
	{
		return $this->insert_id;
	}

}
