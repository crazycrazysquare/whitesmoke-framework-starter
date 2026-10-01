<?php
declare(strict_types=1);

return [
    'default' => getenv('DB_CONNECTION') ?: 'sqlite',

    'connections' => [
        'mysql' => [
            'driver'   => 'mysql',
            'host'     => getenv('DB_HOST') ?: '127.0.0.1',
            'port'     => (int) (getenv('DB_PORT') ?: 3306),
            'database' => getenv('DB_DATABASE') ?: '',
            'username' => getenv('DB_USERNAME') ?: '',
            'password' => getenv('DB_PASSWORD') ?: '',
            'charset'  => 'utf8mb4',
        ],

        'pgsql' => [
            'driver'   => 'pgsql',
            'host'     => getenv('DB_HOST') ?: '127.0.0.1',
            'port'     => (int) (getenv('DB_PORT') ?: 5432),
            'database' => getenv('DB_DATABASE') ?: '',
            'username' => getenv('DB_USERNAME') ?: '',
            'password' => getenv('DB_PASSWORD') ?: '',
            'schema'   => 'public',
            'sslmode'  => getenv('DB_SSLMODE') ?: 'prefer',
        ],

        'sqlite' => [
            'driver'   => 'sqlite',
            'database' => getenv('DB_DATABASE') ?: BASE_PATH . '/storage/database.sqlite',
        ],

        'sqlsrv' => [
            'driver'   => 'sqlsrv',
            'host'     => getenv('DB_HOST') ?: '127.0.0.1',
            'port'     => (int) (getenv('DB_PORT') ?: 1433),
            'database' => getenv('DB_DATABASE') ?: '',
            'username' => getenv('DB_USERNAME') ?: '',
            'password' => getenv('DB_PASSWORD') ?: '',
        ],
    ],
];
