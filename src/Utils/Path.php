<?php

namespace Microwin7\PHPUtils\Utils;

use Microwin7\PHPUtils\Main;
use Microwin7\PHPUtils\Configs\PathConfig;
use function Microwin7\PHPUtils\ar_slash_string;
use function Microwin7\PHPUtils\str_ends_with_slash;

class Path extends PathConfig
{
    public static function ROOT_FOLDER(): string
    {
        return str_ends_with_slash(getenv()['ROOT_FOLDER'] ?? parent::ROOT_FOLDER);
    }
    /** SET ENV SCRIPT_DIR
     * Example:
     * getenv()['SCRIPT_DIR'] ?? putenv('SCRIPT_DIR=texture-provider');
     */
    public static function SCRIPT_DIR(): string
    {
        return ar_slash_string(getenv()['SCRIPT_DIR'] ?? throw new \RuntimeException('NEED SET SCRIPT_DIR ENV'));
    }
    public static function DB_LOG_FOLDER(): string
    {
        return str_ends_with_slash(getenv()['DB_LOG_FOLDER'] ?? parent::DB_LOG_FOLDER);
    }
    public static function SITE_TEMPLATES_FOLDER(): string
    {
        return ar_slash_string(getenv()['SITE_TEMPLATES_FOLDER'] ?? parent::SITE_TEMPLATES_FOLDER);
    }
    public static function ITEM_SHOP_PATH_IN_TEMPLATES(): string
    {
        return ar_slash_string(getenv()['ITEM_SHOP_PATH_IN_TEMPLATES'] ?? parent::ITEM_SHOP_PATH_IN_TEMPLATES);
    }
    public static function ITEM_SHOP_IMAGES(): string
    {
        return self::ROOT_FOLDER() . self::SITE_TEMPLATES_FOLDER() . self::ITEM_SHOP_PATH_IN_TEMPLATES();
    }
    public static function URL_ITEM_SHOP_IMAGES(string $image_name, string $category): string
    {
        return Main::getPublicApplicationURL() . self::SITE_TEMPLATES_FOLDER() . self::ITEM_SHOP_PATH_IN_TEMPLATES() .
            (empty($category) ?: ar_slash_string($category)) .
            (empty($image_name) ?: $image_name);
    }
}
