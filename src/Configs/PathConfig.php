<?php

namespace Microwin7\PHPUtils\Configs;

use Microwin7\PHPUtils\Main;
use function Microwin7\PHPUtils\ar_slash_string;
use function Microwin7\PHPUtils\str_ends_with_slash;

class PathConfig
{
    /**
     * Укажите root до публичного корня сайта
     * Пример: /var/www/html/
     */
    protected const string ROOT_FOLDER = '/var/www/html/';
    /**
     * Логи БД
     */
    protected const string DB_LOG_FOLDER = '/var/www/db_logs/';
    # --- OPTIONAL --- #
    protected const string SITE_TEMPLATES_FOLDER = 'templates/имя_шаблона/';
    protected const string ITEM_SHOP_PATH_IN_TEMPLATES = 'images/item_shop/';
}
