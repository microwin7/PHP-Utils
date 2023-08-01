<?php

namespace Microwin7\PHPUtils\Security;

use Microwin7\PHPUtils\Configs\MainConfig;

class BearerToken
{
    public static function getBearer(): string
    {
        return substr(@array_change_key_case(getallheaders())['authorization'], 7) ?? '';
    }
    public static function validationBearer(): bool
    {
        return self::getBearer() === MainConfig::BEARER_TOKEN;
    }
}
