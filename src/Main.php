<?php

namespace Microwin7\PHPUtils;

use Microwin7\PHPUtils\Utils\Path;
use Microwin7\PHPUtils\DB\SubDBTypeEnum;
use Microwin7\PHPUtils\DB\DriverTypeEnum;
use Microwin7\PHPUtils\Configs\MainConfig;
use function Microwin7\PHPUtils\str_ends_with_slash;
use Microwin7\PHPUtils\Exceptions\ServerNotFoundException;
use Microwin7\PHPUtils\Exceptions\AliasServerNotFoundException;

class Main extends MainConfig
{
    /**
     * WEB адресс приложения
     * Вид: '<http|https>://<IP|IP:PORT|DOMAIN>/'
     * Пример: 'http://127.0.0.1:80/'
     * Use Main::getPublicApplicationURL()
     */
    private const string APP_URL = 'http://127.0.0.1:80/';
    // Подключение к БД сайта
    private const string DB_HOST = 'localhost';
    private const string DB_NAME = 'test';
    private const string DB_USER = 'test';
    private const string DB_PASS = 'test';
    private const int DB_PORT = 3306;
    /**
     * НЕ МЕНЯТЬ, ЕДИНСТВЕННЫЙ ПОДДЕРЖИВАЕМЫЙ ДРАЙВЕР
     * @deprecated v1.7.0.3
     * DriverTypeEnum::PDO [SubDBTypeEnum::MySQL, SubDBTypeEnum::PostgreSQL]
     */
    public const DriverTypeEnum DB_DRIVER = DriverTypeEnum::PDO;
    /**
     * DSN префикс Sub DB
     * SubDBTypeEnum::MySQL
     * SubDBTypeEnum::PostgreSQL
     */
    private const SubDBTypeEnum DB_SUD_DB = SubDBTypeEnum::MySQL;
    // Префикс БД для SERVERS
    private const string DB_PREFIX_SERVERS = 'server_';
    // Запись в файлы лога SQL запросов и их ошибок
    private const bool DB_DEBUG = true;
    private const string|null BEARER_TOKEN = null;
    private const string PRIVATE_API_KEY = '';
    // https://base64.guru/converter/encode/file
    private const string LAUNCH_SERVER_ECDSA256_PUBLIC_KEY_BASE64 = 'MFkwEwYHKoZIzj0CAQYIKoZIzj0DAQcDQgAEJDi51DKs5f6ERSrDDjns00BkI963L9OS9wLA2Ak/nACZCgQma+FsTsbYtZQm4nk+rtabM8b9JgzSi3sPINb8fg==';
    private const bool SENTRY_ENABLE = false;
    private const string|null SENTRY_DSN = null;

    public static function getApplicationURL(bool $needle_ends_with_slash = TRUE): string
    {
        return str_ends_with_slash(getenv()['APP_URL'] ?? self::APP_URL, $needle_ends_with_slash);
    }
    public static function getScriptURL(): string
    {
        return self::getApplicationURL() . Path::SCRIPT_PATH();
    }
    /**
     * Поиск и возвращение имени сервера, если оно есть в алиас или главном имени, возвращает главное имя. Если сервер не найден, возвращает {@link \Microwin7\PHPUtils\Exceptions\ServerNotFoundException}
     *
     * @param string $server_name Принимает имя сервера, по которому будет произведён поиск основного имени сервера
     * @return key-of<MainConfig::SERVERS> Найденное имя сервера с учётом регистра, либо исключение
     * 
     * @throws ServerNotFoundException
     */
    public static function getServerWithoutDefault(string $server_name = ''): string
    {
        $servers_list = [];
        $alias_list = [];
        foreach (parent::SERVERS as $k => $v) {
            $servers_list[] = $k;
            $alias_list[] = $v['alias'];
        }
        /** @psalm-suppress RedundantFunctionCall */
        $key = array_search(strtolower($server_name), array_map('strtolower', $servers_list));
        if ($key !== false) return $servers_list[$key];
        foreach ($alias_list as $k => $v) {
            /** @psalm-suppress RedundantFunctionCall */
            $alias_key = array_search(strtolower($server_name), array_map('strtolower', $v));
            if ($alias_key !== false) return $servers_list[$k];
        }
        throw new ServerNotFoundException;
    }
    /**
     * Получение только главных имён серверов
     *
     * @return array<key-of<MainConfig::SERVERS>>
     */
    public static function getListServers(): array
    {
        $servers_list = [];
        foreach (parent::SERVERS as $k => $_) {
            $servers_list[] = $k;
        }
        return $servers_list;
    }
    /**
     * Поиск и возвращение только по алиас именам сервера, возвращает алиас имя. Если алиас имя среди серверов не найдено, возвращает {@link \Microwin7\PHPUtils\Exceptions\AliasServerNotFoundException}
     *
     * @param key-of<MainConfig::SERVERS>|string $server_name Принимает алиас имя сервера, для поиска соответствия имени
     * @return string Найденное алиас имя сервера с учётом регистра, либо исключение
     * 
     * @throws AliasServerNotFoundException
     */
    public static function getAliasServerWithoutDefault(string $server_name = ''): string
    {
        $alias_list = [];
        foreach (parent::SERVERS as $v) {
            $alias_list[] = $v['alias'];
        }
        foreach ($alias_list as $v) {
            /** @psalm-suppress RedundantFunctionCall */
            $alias_key = array_search(strtolower($server_name), array_map('strtolower', $v));
            if ($alias_key !== false) return $v[$alias_key];
        }
        throw new AliasServerNotFoundException;
    }
    /**
     * Получение только первого алиас именени всех серверов
     *
     * @return string[]
     */
    public static function getPrimaryAliasListServers(): array
    {
        $alias_list = [];
        foreach (parent::SERVERS as $v) {
            $alias_list[] = $v['alias'][0];
        }
        return $alias_list;
    }
    /**
     * Поиск главного сервера, либо возвращение первого в массиве серверов (Сервер по умолчанию)
     * 
     * @param string $server_name
     * @return key-of<MainConfig::SERVERS> Возвращает строку найденного имя сервера с учётом регистра
     */
    public static function getServer(string $server_name = ''): string
    {
        try {
            return self::getServerWithoutDefault($server_name);
        } catch (\Microwin7\PHPUtils\Exceptions\ServerNotFoundException) {
            return array_key_first(parent::SERVERS);
        }
    }
    /**
     * Поиск главного сервера, либо возвращение первого в массиве серверов (Сервер по умолчанию)
     *
     * @param string $server_name Передаётся переменная по ссылке, будет перезаписана вне функции
     * @return key-of<MainConfig::SERVERS> Возвращает строку найденного имя сервера с учётом регистра
     */
    public static function getCorrectServer(&$server_name = ''): string
    {
        try {
            return $server_name = self::getServerWithoutDefault($server_name);
        } catch (\Microwin7\PHPUtils\Exceptions\ServerNotFoundException) {
            return $server_name = array_key_first(parent::SERVERS);
        }
    }
    public static function DB_HOST(): string
    {
        return getenv()[__FUNCTION__] ?? self::DB_HOST;
    }
    public static function DB_NAME(): string
    {
        return getenv()[__FUNCTION__] ?? self::DB_NAME;
    }
    public static function DB_USER(): string
    {
        return getenv()[__FUNCTION__] ?? self::DB_USER;
    }
    public static function DB_PASS(): string
    {
        return getenv()[__FUNCTION__] ?? self::DB_PASS;
    }
    /** @throws \RuntimeException */
    public static function DB_PORT(): int
    {
        return ($ENV = getenv(__FUNCTION__)) === false ?
            self::DB_PORT : (
                ($ENV_INT = filter_var($ENV, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 65535]])) === false ?
                throw new \RuntimeException(sprintf('Invalid value set in environment %s: %s', __FUNCTION__, $ENV)) :
                $ENV_INT
            );
    }
    /** @throws \RuntimeException */
    public static function DB_SUD_DB(): SubDBTypeEnum
    {
        return ($ENV = getenv(__FUNCTION__)) === false ?
            self::DB_SUD_DB : (
                ($ENUM = SubDBTypeEnum::tryFromString($ENV)) === null ?
                throw new \RuntimeException(sprintf('Invalid value set in environment %s: %s', __FUNCTION__, $ENV)) :
                $ENUM
            );
    }
    public static function DB_PREFIX_SERVERS(): string
    {
        return getenv()[__FUNCTION__] ?? self::DB_PREFIX_SERVERS;
    }
    public static function DB_DEBUG(): bool
    {
        return ($ENV = getenv(__FUNCTION__)) === false ? self::DB_DEBUG : filter_var($ENV, FILTER_VALIDATE_BOOLEAN);
    }
    public static function BEARER_TOKEN(): string|null
    {
        return ($ENV = getenv(__FUNCTION__)) === false ? self::BEARER_TOKEN : (strtolower($ENV) === 'null' ? null : $ENV);
    }
    public static function PRIVATE_API_KEY(): string
    {
        return getenv()[__FUNCTION__] ?? self::PRIVATE_API_KEY;
    }
    public static function SENTRY_ENABLE(): bool
    {
        return ($ENV = getenv(__FUNCTION__)) === false ? self::SENTRY_ENABLE : filter_var($ENV, FILTER_VALIDATE_BOOLEAN);
    }
    public static function SENTRY_DSN(): string|null
    {
        return ($ENV = getenv(__FUNCTION__)) === false ? self::SENTRY_DSN : (strtolower($ENV) === 'null' ? null : $ENV);
    }
    public static function getLaunchServerPublicKey(): string
    {
        return
            "-----BEGIN PUBLIC KEY-----\n" .
            chunk_split((getenv()['LAUNCH_SERVER_ECDSA256_PUBLIC_KEY_BASE64'] ?? self::LAUNCH_SERVER_ECDSA256_PUBLIC_KEY_BASE64), 64, "\n") .
            "-----END PUBLIC KEY-----";
    }
}
