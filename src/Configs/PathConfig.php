<?php

namespace Microwin7\PHPUtils\Configs;

define("DOCUMENT_ROOT", $_SERVER['DOCUMENT_ROOT'] . '/');

class PathConfig
{
    /**
     * WEB адресс приложения
     * Вид: '<http|https>://<IP|IP:PORT|DOMAIN>/'
     * Пример: 'http://127.0.0.1:80/'
     */
    public const APP_URL = 'http://127.0.0.1:80/';
    /**
     * Укажите root до публичного корня сайта
     * Пример: /var/www/html/
     */
    public const ROOT_FOLDER = DOCUMENT_ROOT;
    /**
     * Логи БД
     */
    public const DB_LOG_FOLDER = '/var/www/db_logs/';
    public const SITE_TEMPLATES_FOLDER = 'templates/имя_шаблона/';
    public const ITEM_SHOP_IMAGES =  self::ROOT_FOLDER . '/' . self::SITE_TEMPLATES_FOLDER . 'images/item_shop/';
    public const URL_ITEM_SHOP_IMAGES =  '/' . self::SITE_TEMPLATES_FOLDER . 'images/item_shop/';
}
