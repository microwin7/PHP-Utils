<?php

namespace Microwin7\PHPUtils;

use Microwin7\PHPUtils\Configs\MainConfig;
use Microwin7\PHPUtils\DB\SubDBTypeEnum;
use Microwin7\PHPUtils\Exceptions\ServerNotFoundException;
use Microwin7\PHPUtils\Exceptions\AliasServerNotFoundException;
use function Microwin7\PHPUtils\str_ends_with_slash;

class Main extends MainConfig
{
    public static function getPublicApplicationURL(bool $needle_ends_with_slash = TRUE): string
    {
        return str_ends_with_slash(getenv()['APP_URL'] ?? parent::APP_URL, $needle_ends_with_slash);
    }
    /**
     * Поиск и возвращение имени сервера, если оно есть в алиас или главном имени, возвращает главное имя. Если сервер не найден, возвращает {@link \Microwin7\PHPUtils\Exceptions\ServerNotFoundException}
     *
     * @param string $server_name Принимает имя сервера, по которому будет произведён поиск основного имени сервера
     * @return key-of<MainConfig::SERVERS> Найденное имя сервера с учётом регистра, либо исключение
     * 
     * @throws ServerNotFoundException
     */
    public static function getServerWithoutDefault(string $server_name = '')
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
    public static function getAliasServerWithoutDefault(string $server_name = '')
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
        } catch (\Microwin7\PHPUtils\Exceptions\ServerNotFoundException $e) {
            return array_key_first(parent::SERVERS);
        }
    }
    /**
     * Поиск главного сервера, либо возвращение первого в массиве серверов (Сервер по умолчанию)
     *
     * @param string $server_name Передаётся переменная по ссылке, будет перезаписана вне функции
     * @return key-of<MainConfig::SERVERS> Возвращает строку найденного имя сервера с учётом регистра
     */
    public static function getCorrectServer(&$server_name = '')
    {
        try {
            return $server_name = self::getServerWithoutDefault($server_name);
        } catch (\Microwin7\PHPUtils\Exceptions\ServerNotFoundException $e) {
            return $server_name = array_key_first(parent::SERVERS);
        }
    }
    public static function DB_HOST(): string
    {
        return getenv()[__FUNCTION__] ?? parent::DB_HOST;
    }
    public static function DB_NAME(): string
    {
        return getenv()[__FUNCTION__] ?? parent::DB_NAME;
    }
    public static function DB_USER(): string
    {
        return getenv()[__FUNCTION__] ?? parent::DB_USER;
    }
    public static function DB_PASS(): string
    {
        return getenv()[__FUNCTION__] ?? parent::DB_PASS;
    }
    /** @throws \RuntimeException */
    public static function DB_PORT(): int
    {
        return ($ENV = getenv(__FUNCTION__)) === false ?
            parent::DB_PORT : (
                ($ENV_INT = filter_var($ENV, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 65535]])) === false ?
                throw new \RuntimeException(sprintf('Invalid value set in environment %s: %s', __FUNCTION__, $ENV)) :
                $ENV_INT
            );
    }
    /** @throws \RuntimeException */
    public static function DB_SUD_DB(): SubDBTypeEnum
    {
        return ($ENV = getenv(__FUNCTION__)) === false ?
            parent::DB_SUD_DB : (
                ($ENUM = SubDBTypeEnum::tryFromString($ENV)) === null ?
                throw new \RuntimeException(sprintf('Invalid value set in environment %s: %s', __FUNCTION__, $ENV)) :
                $ENUM
            );
    }
    public static function DB_PREFIX_SERVERS(): string
    {
        return getenv()[__FUNCTION__] ?? parent::DB_PREFIX_SERVERS;
    }
    public static function DB_DEBUG(): bool
    {
        return ($ENV = getenv(__FUNCTION__)) === false ? parent::DB_DEBUG : filter_var($ENV, FILTER_VALIDATE_BOOLEAN);
    }
    public static function BEARER_TOKEN(): string|null
    {
        return ($ENV = getenv(__FUNCTION__)) === false ? parent::BEARER_TOKEN : (strtolower($ENV) === 'null' ? null : $ENV);
    }
    public static function PRIVATE_API_KEY(): string
    {
        return getenv()[__FUNCTION__] ?? parent::PRIVATE_API_KEY;
    }
    public static function SENTRY_ENABLE(): bool
    {
        return ($ENV = getenv(__FUNCTION__)) === false ? parent::SENTRY_ENABLE : filter_var($ENV, FILTER_VALIDATE_BOOLEAN);
    }
    public static function SENTRY_DSN(): string|null
    {
        return ($ENV = getenv(__FUNCTION__)) === false ? parent::SENTRY_DSN : (strtolower($ENV) === 'null' ? null : $ENV);
    }
    public static function getLaunchServerPublicKey(): string
    {
        return
            "-----BEGIN PUBLIC KEY-----\n" .
            chunk_split((getenv()['LAUNCH_SERVER_ECDSA256_PUBLIC_KEY_BASE64'] ?? parent::LAUNCH_SERVER_ECDSA256_PUBLIC_KEY_BASE64), 64, "\n") .
            "-----END PUBLIC KEY-----";
    }
}
