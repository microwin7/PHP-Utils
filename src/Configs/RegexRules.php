<?php

namespace Microwin7\PHPUtils\Configs;

class RegexRules
{
    public const DISALLOW_FIRST_SPACE = '/^\s/';
    public const DISALLOW_LAST_SPACE = '/\s$/';
    public const ID_REGXP = '/^[a-zA-Z0-9\-\_\:\.]*$/';
    public const DISPLAYNAME_REGXP = '/^([a-zA-Zа-яА-ЯЁё0-9\-\_\ \(\)\[\]\.\,\"\«\»\/])*$/u';
    public const NUMERIC_REGXP = '/^[0-9]*$/';
    public const CATEGORY_REGXP = '/^([A-Z0-9\_]+)*$/';
    public const ITEM_LINK_REGXP = '/^([a-zA-Z0-9\_\-\.]+)*$/';
    public const SERVER_REGXP = '/^([a-zA-Z]+)*$/';
    public const REGEX_UUIDv1_AND_v4 = "/[0-9a-f]{8}-(?:[0-9a-f]{4}-){3}[0-9a-f]{12}/";
}
