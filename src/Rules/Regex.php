<?php

namespace Microwin7\PHPUtils\Rules;

class Regex
{
    public const DISALLOW_FIRST_SPACE = '/^\s/';
    public const DISALLOW_LAST_SPACE = '/\s$/';
    public const ID_REGXP = '/^[a-zA-Z0-9\-\_\:\.]*$/';
    public const DISPLAYNAME_REGXP = '/^([a-zA-Zа-яА-ЯЁё0-9\-\_\ \(\)\[\]\.\,\"\«\»\/])*$/u';
    public const NUMERIC_REGXP = '/^[0-9]*$/';
    public const CATEGORY_REGXP = '/^([A-Z0-9\_]+)*$/';
    public const ITEM_LINK_REGXP = '/^([a-zA-Z0-9\_\-\.]+)*$/';
    public const SERVER_REGXP = '/^([a-zA-Z]+)*$/';
    public const USERNAME = "\w{1,16}$";
    public const UUID_NO_DASH = "[0-9a-f]{32}";
    public const UUIDv1_AND_v4 = "/[0-9a-f]{8}-(?:[0-9a-f]{4}-){3}[0-9a-f]{12}/";

    public static function valid_username(string $string): bool
    {
        return (!is_null($string) && (preg_match("/^" . self::USERNAME . "/", $string, $varR)));
    }
    public static function valid_uuid_no_dash(string $string): bool
    {
        return (!is_null($string) && preg_match("/" . self::UUID_NO_DASH . "/", str_replace('-', '', $string), $varR));
    }
    public static function contains(string $haystack, string $needle)
    {
        return strpos($haystack, $needle) !== false;
    }
}
