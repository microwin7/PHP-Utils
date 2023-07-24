<?php

namespace Microwin7\PHPUtils\Utils;

use \Microwin7\PHPUtils\Configs\Main;
use \Microwin7\PHPUtils\Configs\Path;

class DBDebug
{
	private function file_put_contents($path, $message)
	{
		$directory = Path::DB_LOG_FOLDER;
		if (defined('DB_MODULE_NAME')) {
			if (defined('DB_MODULE_COMPONENT_NAME')) $directory .= constant('DB_MODULE_NAME') . '/' . constant('DB_MODULE_COMPONENT_NAME') . '/';
			else $directory .= constant('DB_MODULE_NAME') . '/';
		} else $directory .= substr(strrchr(realpath('.'), '/'), 1) . '/';
		if (!file_exists($directory))
			mkdir($directory, 0777, true);
		file_put_contents($directory . $path . '_' . date("Y.n") . '.log', date('[d] | H:i:s - ') . $message . "\n", FILE_APPEND);
	}
	public function debug($message)
	{
		if (Main::DEBUG) $this->file_put_contents(__FUNCTION__, $message);
	}
	public function debug_error($message)
	{
		if (Main::DEBUG) $this->file_put_contents(__FUNCTION__, $message);
	}
}
