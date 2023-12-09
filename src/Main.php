<?php

namespace Microwin7\PHPUtils;

use Microwin7\PHPUtils\Configs\MainConfig;
use Microwin7\PHPUtils\Exceptions\ServerNotFoundException;
use Microwin7\PHPUtils\Exceptions\AliasServerNotFoundException;

class Main extends MainConfig
{
    /**
     * Поиск и возвращение имени сервера, если оно есть в алиас или главном имени, возвращает главное имя. Если сервер не найден, возвращает {@link \Microwin7\PHPUtils\Exceptions\ServerNotFoundException}
     *
     * @param string $server_name Принимает имя сервера, по которому будет произведён поиск основного имени сервера
     * @return string Найденное имя сервера с учётом регистра, либо исключение
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
        $key = array_search(strtolower($server_name), array_map('strtolower', $servers_list));
        if ($key !== false) return $servers_list[$key];
        foreach ($alias_list as $k => $v) {
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
    public static function getPublicKeyFromBase64(): string
    {
        return "-----BEGIN PUBLIC KEY-----\n" . chunk_split(parent::ECDSA256_PUBLIC_KEY_BASE64, 64, "\n") . "-----END PUBLIC KEY-----";
    }
    public static function getPublicKeyFromBytes(): string
    {
        return "-----BEGIN PUBLIC KEY-----\n" . chunk_split(base64_encode(file_get_contents(parent::ECDSA256_PUBLIC_KEY_PATH)), 64, "\n") . "-----END PUBLIC KEY-----";
    }
}
