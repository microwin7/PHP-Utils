<?php

namespace Microwin7\PHPUtils\Configs;

class MainConfig
{
    public const array DB_PDO_OPTIONS = [
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_PERSISTENT => true
    ];

    /** @var array<string, array<string, mixed>> */
    public const array SERVERS = [
        'имя1' => [         // Сервера с именем бд всегда в нижнем регистре, префикс для серверов 'server_' полное имя бд: 'server_имя1'
            'alias' => ['алиас_имя1'], // Алиасы имён сервера, по которым будет вызвано основное имя для подключение к бд
            'host' => '127.0.0.1',
            'port' => 25565,
            'rcon' => [
                'enable' => true,
                'password' => '',
                'port' => 25575,
                'timeout' => 3,
            ],
        ],
        'имя2' => [
            'alias' => ['алиас_имя2', 'алиас2_имя2'],
            'host' => '127.0.0.1',
            'port' => 25566,
            'rcon' => [
                'enable' => false,
                'password' => '',
                'port' => 25576,
                'timeout' => 3,
            ],
        ]
    ];
    /** @var array<string, array<string, string|array<string, string>>> */
    public const array MODULES = [
        'TextureProvider' => [
            /** Driver Connect Database */
            'table_user' => [
                'TABLE_NAME' => 'users',
                /**
                 * Колонка связывания с table_user_assets
                 * Либо для получения User ID
                 * Example:
                 * 'user_id' for UserStorageTypeEnum::DB_USER_ID,
                 */
                'id_column' => 'user_id',
                'username_column' => 'username',
                'uuid_column' => 'uuid',
                'email_column' => 'email',
            ],
            /**
             * For UserStorageTypeEnum::DB_SHA1
             * or UserStorageTypeEnum::DB_SHA256
             */
            'table_user_assets' => [
                'TABLE_NAME' => 'user_assets',
                /**
                 * Колонка связывания с table_user
                 */
                'id_column' => 'user_id',
                /**
                 * key-of<ResponseTypeEnum::SKIN|ResponseTypeEnum::CAPE>
                 */
                'texture_type_column' => 'type',
                'hash_column' => 'hash',
                /** NULL(int 0)|SLIM(int 1) */
                'texture_meta_column' => 'meta',
            ],
        ],
    ];
}
