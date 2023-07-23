<?php

namespace Microwin7\PHPUtils\Configs;

class Main
{
    // Подключение к БД сайта
    public const DB_HOST = 'localhost';
    public const DB_NAME = 'test';
    public const DB_USER = 'test';
    public const DB_PASS = 'test';
    public const DB_PORT = '3306';
    public const DB_DRIVER = 'PDO'; // MySQLi, PDO | Default: MySQLi
    public const DB_SUD_DB = 'mysql'; // DSN префикс для PDO [mysql, pgsql] | Default: mysql
    public const DB_PREFIX = 'server_';
    public const DEBUG = true;
    public const PRIVATE_API_KEY = '';
    private const ECDSA256_PUBLIC_KEY_BASE64 = '';
    private const ECDSA256_PUBLIC_KEY_PATH = '';

    public const SERVERS = [
        'имя1' => [         // Сервера с именем бд всегда в нижнем регистре, префикс для серверов 'server_' полное имя бд: 'server_имя1'
            'alias' => ['алиас_имя1'], // Алиасы имён сервера, по которым будет вызвано основное имя для подключение к бд
            'host' => '127.0.0.1',
            'port' => '25565',
            'rcon' => [
                'enable' => true,
                'password' => '',
                'timeout' => 3,
            ],
        ],
        'имя2' => [
            'alias' => ['алиас_имя2', 'алиас2_имя2'],
            'host' => '127.0.0.1',
            'port' => '25566',
            'rcon' => [
                'enable' => false,
                'password' => '',
                'timeout' => 3,
            ],
        ]
    ];
    public const MODULES = [
        'LuckPerms' => [
            'DB_NAME' => 'LuckPerms',
            'prefix' => 'luckperms_',
        ],
        'LiteBans' => [
            'DB_NAME' => 'LiteBans',
            'prefix' => 'litebans_',
        ],
    ];
    /**
     * Поиск и возвращение имени сервера, если оно есть в алиас или главном имени, возвращает главное имя. Если сервер не найден, возвращает {@link \Microwin7\PHPUtils\Exceptions\ServerNotFound}
     *
     * @param string $server_name Принимает имя сервера, по которому будет произведён поиск основного имени сервера
     * @return string|\Microwin7\PHPUtils\Exceptions\ServerNotFound Найденное имя сервера с учётом регистра, либо исключение
     */
    public static function getServerWithoutDefault(string $server_name = '')
    {
        $servers_list = [];
        $alias_list = [];
        foreach (self::SERVERS as $k => $v) {
            $servers_list[] = $k;
            $alias_list[] = $v['alias'];
        }
        $key = array_search(strtolower($server_name), array_map('strtolower', $servers_list));
        if ($key !== false) return $servers_list[$key];
        foreach ($alias_list as $k => $v) {
            $alias_key = array_search(strtolower($server_name), array_map('strtolower', $v));
            if ($alias_key !== false) return $servers_list[$k];
        }
        throw new \Microwin7\PHPUtils\Exceptions\ServerNotFound;
    }
    /**
     * Получение только главных имён серверов
     *
     * @return array
     */
    public static function getListServers(): array
    {
        $servers_list = [];
        foreach (self::SERVERS as $k => $v) {
            $servers_list[] = $k;
        }
        return $servers_list;
    }
    /**
     * Поиск и возвращение только по алиас именам сервера, возвращает алиас имя. Если алиас имя среди серверов не найдено, возвращает {@link \Microwin7\PHPUtils\Exceptions\AliasServerNotFound}
     *
     * @param string $server_name Принимает алиас имя сервера, для поиска соответствия имени
     * @return string|\Microwin7\PHPUtils\Exceptions\AliasServerNotFound Найденное алиас имя сервера с учётом регистра, либо исключение
     */
    public static function getAliasServerWithoutDefault(string $server_name = '')
    {
        $alias_list = [];
        foreach (self::SERVERS as $v) {
            $alias_list[] = $v['alias'];
        }
        foreach ($alias_list as $k => $v) {
            $alias_key = array_search(strtolower($server_name), array_map('strtolower', $v));
            if ($alias_key !== false) return $v[$alias_key];
        }
        throw new \Microwin7\PHPUtils\Exceptions\AliasServerNotFound;
    }
    /**
     * Получение только первого алиас именени всех серверов
     *
     * @return array
     */
    public static function getPrimaryAliasListServers(): array
    {
        $alias_list = [];
        foreach (self::SERVERS as $v) {
            $alias_list[] = $v['alias'][0];
        }
        return $alias_list;
    }
    /**
     * Поиск главного сервера, либо возвращение первого в массиве серверов (Сервер по умолчанию)
     *
     * @param string $server_name
     * @return string Возвращает строку найденного имя сервера с учётом регистра
     */
    public static function getServer(string $server_name = ''): string
    {
        try {
            return self::getServerWithoutDefault($server_name);
        } catch (\Microwin7\PHPUtils\Exceptions\ServerNotFound $e) {
            return array_key_first(self::SERVERS);
        }
    }
    /**
     * Поиск главного сервера, либо возвращение первого в массиве серверов (Сервер по умолчанию)
     *
     * @param string $server_name Передаётся переменная по ссылке, будет перезаписана вне функции
     * @return string Возвращает строку найденного имя сервера с учётом регистра
     */
    public static function getCorrectServer(&$server_name = '')
    {
        try {
            return $server_name = self::getServerWithoutDefault($server_name);
        } catch (\Microwin7\PHPUtils\Exceptions\ServerNotFound $e) {
            return $server_name = array_key_first(self::SERVERS);
        }
    }
	public static function getPublicKeyFromBase64()
	{
		return "-----BEGIN PUBLIC KEY-----\n" . chunk_split(self::ECDSA256_PUBLIC_KEY_BASE64, 64, "\n") . "-----END PUBLIC KEY-----";
	}
    public static function getPublicKeyFromBytes()
	{
		return "-----BEGIN PUBLIC KEY-----\n" . chunk_split(base64_encode(file_get_contents(self::ECDSA256_PUBLIC_KEY_PATH)), 64, "\n") . "-----END PUBLIC KEY-----";
	}
}
