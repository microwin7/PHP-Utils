<?php

declare(strict_types=1);

namespace Microwin7\PHPUtils\Rules;

class Regex
{
    public const string DISALLOW_FIRST_SPACE = '/^\s/';
    public const string DISALLOW_LAST_SPACE = '/\s$/';
    public const string ID_REGXP = '/^[a-zA-Z0-9\-\_\:\.]+$/';
    public const string DISPLAYNAME_REGXP = '/^[a-zA-Zа-яА-ЯЁё0-9\-\_\ \(\)\[\]\.\,\"\«\»\/]+$/';
    public const string NUMERIC_REGXP = '/^[0-9]+$/';
    public const string BOOLEAN_REGXP = '/^(0|1|[Tt][Rr][Uu][Ee]|[Ff][Aa][Ll][Ss][Ee])$/';
    public const string CATEGORY_REGXP = '/^[A-Z0-9\_]+$/';
    public const string ITEM_LINK_REGXP = '/^[a-zA-Z0-9\_\-\.]+$/';
    public const string SERVER_REGXP = '/^[a-zA-Z\_]+$/';
    public const string LOGIN = '/^[a-zA-Z0-9_-]{1,64}$/';
    public const string USERNAME = '/^\w{2,16}$/';
    public const string UUID_NO_DASH = '/^[0-9a-f]{32}$/';
    public const string UUIDv1_AND_v4 = '/^[0-9a-f]{8}-(?:[0-9a-f]{4}-){3}[0-9a-f]{12}$/';
    public const string MD5 = self::UUID_NO_DASH;
    public const string SHA1 = '/^[0-9a-f]{40}$/';
    public const string SHA256 = '/^[0-9a-f]{64}$/';
    public const string ENV_NAME = '/^([A-Z0-9\_]+)=(.*?)$/';

    /**
     * @throws \InvalidArgumentException
     */
    public static function valid_with_pattern(string $string, string $pattern): bool
    {
        return empty($pattern) ? throw new \InvalidArgumentException($pattern) : (bool)preg_match($pattern, $string, $varR);
    }
    public static function valid_username(string $string): bool
    {
        return (bool)preg_match(self::USERNAME, $string, $varR);
    }
    public static function valid_uuid(string $string): bool
    {
        return (bool)preg_match(self::UUIDv1_AND_v4, $string, $varR);
    }
    public static function valid_uuid_no_dash(string $string): bool
    {
        return (bool)preg_match(self::UUID_NO_DASH, str_replace('-', '', $string), $varR);
    }
    public static function contains(string $haystack, string $needle): bool
    {
        return strpos($haystack, $needle) !== false;
    }
    public static function combineOR(string ...$patternArray): string
    {
        $combinedPattern = implode('|', array_map(function ($pattern) {
            return rtrim(ltrim($pattern, '/'), '/');
        }, $patternArray));
        return '/' . $combinedPattern . '/';
    }
}
