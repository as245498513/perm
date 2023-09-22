<?php

return [
    // sdk 包名
    'name' => 'permission',

    // 应用 Id
    'app_id' => env('APP_ID', ''),

    // 数据源
    'driver' => env('PERMISSION_DRIVER', 'database'),

    // http 配置
    'http' => [
        // basic 认证用户名密码
        'api_key'  => env('PERMISSION_HTTP_USER', '66fd1d82b9a9444a675335906ed20f1d'),
        'password' => env('PERMISSION_HTTP_PASS', 'e405af04d4f96f595b75485fead13004'),

        // 接口域名
        'base_uri' => env('PERMISSION_BASE_URI', ''),
    ],

    // 数据库连接配置
    'connections' => [
        'permission' => [
            'driver' => 'mysql',
            'host' => env('PERMISSION_DB_HOST', '127.0.0.1'),
            'port' => env('PERMISSION_DB_PORT', 3306),
            'database' => env('PERMISSION_DB_DATABASE', 'test'),
            'username' => env('PERMISSION_DB_USERNAME', 'root'),
            'password' => env('PERMISSION_DB_PASSWORD', 'abc123'),
            'unix_socket' => env('PERMISSION_DB_SOCKET', ''),
            'charset' => env('PERMISSION_DB_CHARSET', 'utf8mb4'),
            'collation' => env('PERMISSION_DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => env('PERMISSION_DB_PREFIX', ''),
            'strict' => env('PERMISSION_DB_STRICT_MODE', true),
            'engine' => env('PERMISSION_DB_ENGINE', null),
            'timezone' => env('PERMISSION_DB_TIMEZONE', '+00:00'),
        ],
    ],

    // 是否记录 sql 查询
    'log_query' => env('PERMISSION_LOG_QUERY', false),

    // 日志 channel 配置
    'logging' => [
        'driver' => 'daily',
        'path' => storage_path("logs/permission-sdk.log"),
        'level' => env('LOG_DEFAULT_LEVEL', 'debug'),
        'days' => 14,
    ],
];
