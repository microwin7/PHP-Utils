<?php

namespace Microwin7\PHPUtils\Configs;

class MainConfig
{
    // Подключение к БД сайта
    public const DB_HOST = 'localhost';
    public const DB_NAME = 'test';
    public const DB_USER = 'test';
    public const DB_PASS = 'test';
    public const DB_PORT = '3306';
    public const DB_DRIVER = 'PDO'; // MySQLi, PDO | Default: MySQLi
    public const DB_SUD_DB = 'mysql'; // DSN префикс для PDO [mysql, pgsql] | Default: mysql
    // Префикс БД для SERVERS
    public const DB_PREFIX = 'server_';
    // Запись в файлы лога SQL запросов и их ошибок
    public const DEBUG = true;
    public const BEARER_TOKEN = '';
    public const PRIVATE_API_KEY = '';
    // https://base64.guru/converter/encode/file
    protected const ECDSA256_PUBLIC_KEY_BASE64 = 'MFkwEwYHKoZIzj0CAQYIKoZIzj0DAQcDQgAEJDi51DKs5f6ERSrDDjns00BkI963L9OS9wLA2Ak/nACZCgQma+FsTsbYtZQm4nk+rtabM8b9JgzSi3sPINb8fg==';
    protected const ECDSA256_PUBLIC_KEY_PATH = '';

    public const SERVERS = [
        'имя1' => [         // Сервера с именем бд всегда в нижнем регистре, префикс для серверов 'server_' полное имя бд: 'server_имя1'
            'alias' => ['алиас_имя1'], // Алиасы имён сервера, по которым будет вызвано основное имя для подключение к бд
            'host' => '127.0.0.1',
            'port' => '25565',
            'rcon' => [
                'enable' => true,
                'password' => '',
                'timeout' => 3,
            ],
        ],
        'имя2' => [
            'alias' => ['алиас_имя2', 'алиас2_имя2'],
            'host' => '127.0.0.1',
            'port' => '25566',
            'rcon' => [
                'enable' => false,
                'password' => '',
                'timeout' => 3,
            ],
        ]
    ];
    public const MODULES = [
        'LuckPerms' => [
            'DB_NAME' => 'LuckPerms',
            'prefix' => 'luckperms_',
        ],
        'LiteBans' => [
            'DB_NAME' => 'LiteBans',
            'prefix' => 'litebans_',
        ],
    ];

    // Path
    public const ROOT_FOLDER = '/var/www/html/';
    public const DB_LOG_FOLDER = '/var/www/db_logs/';
    public const SITE_TEMPLATES_FOLDER = 'templates/имя_шаблона/';

    // Texture
    protected const TEXTURE_PATH = [
        'skin' => self::ROOT_FOLDER . 'skins/',
        'cape' => self::ROOT_FOLDER . 'capes/'
    ];
    protected const MAX_SIZE_BYTES = 2 * 1024 * 1024; // byte => Kbyte, Kbyte => MB * 2
}
