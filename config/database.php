<?php
declare(strict_types=1);

return [
    'default' => env('DB_CONNECTION', 'sqlite'),

    'connections' => [
        'mysql' => [
            'driver'   => 'mysql',
            'host'     => (string) env('DB_HOST', '127.0.0.1'),
            'port'     => (int) env('DB_PORT', 3306),
            'database' => (string) env('DB_DATABASE', ''),
            'username' => (string) env('DB_USERNAME', ''),
            'password' => (string) env('DB_PASSWORD', ''),
            'charset'  => 'utf8mb4',
        ],

        'pgsql' => [
            'driver'   => 'pgsql',
            'host'     => (string) env('DB_HOST', '127.0.0.1'),
            'port'     => (int) env('DB_PORT', 5432),
            'database' => (string) env('DB_DATABASE', ''),
            'username' => (string) env('DB_USERNAME', ''),
            'password' => (string) env('DB_PASSWORD', ''),
            'schema'   => 'public',
            'sslmode'  => (string) env('DB_SSLMODE', 'prefer'),
        ],

        'sqlite' => [
            'driver'   => 'sqlite',
            'database' => (string) env('DB_DATABASE', BASE_PATH . '/storage/database.sqlite'),
        ],

        'sqlsrv' => [
            'driver'   => 'sqlsrv',
            'host'     => (string) env('DB_HOST', '127.0.0.1'),
            'port'     => (int) env('DB_PORT', 1433),
            'database' => (string) env('DB_DATABASE', ''),
            'username' => (string) env('DB_USERNAME', ''),
            'password' => (string) env('DB_PASSWORD', ''),
        ],
    ],
];
