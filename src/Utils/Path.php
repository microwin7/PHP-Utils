<?php

namespace Microwin7\PHPUtils\Utils;

use Microwin7\PHPUtils\Configs\PathConfig;
use function Microwin7\PHPUtils\str_ends_with_slash;

class Path
{
    public static function getAppUrl(bool $needle_ends_with_slash = TRUE): string
    {
        return str_ends_with_slash(PathConfig::APP_URL, $needle_ends_with_slash);
    }
}
