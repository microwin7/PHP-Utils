<?php

namespace Microwin7\PHPUtils\Configs;

class Path
{
    public const ROOT_FOLDER = '/var/www/html/';

    public const DB_LOG_FOLDER = '/var/www/db_logs/';

    public const SITE_TEMPLATES_FOLDER = 'templates/имя_шаблона/';

    public const ITEM_SHOP_IMAGES =  self::ROOT_FOLDER . '/' . self::SITE_TEMPLATES_FOLDER . 'images/item_shop/';
    public const URL_ITEM_SHOP_IMAGES =  '/' . self::SITE_TEMPLATES_FOLDER . 'images/item_shop/';
}