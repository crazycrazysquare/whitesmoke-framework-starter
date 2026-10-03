<?php
declare(strict_types=1);

return [
    // file: storage/cache/data, for one server. database: the "cache" table, shared by
    // every server that uses the database (run php smoke migrate). redis: a Redis server.
    'driver' => (string) env('CACHE_DRIVER', 'file'),
    'path'   => BASE_PATH . '/storage/cache/data',
    'table'  => 'cache',
    'prefix' => '',   // letters, digits, _ . : -; keeps apps that share a store apart

    'redis' => [
        'host'       => (string) env('REDIS_HOST', '127.0.0.1'),
        'port'       => (int) env('REDIS_PORT', 6379),
        'encryption' => (string) env('REDIS_ENCRYPTION', 'none'),   // tls, or none (only on this machine)
        'username'   => (string) env('REDIS_USERNAME', ''),
        'password'   => (string) env('REDIS_PASSWORD', ''),
        'database'   => (int) env('REDIS_DATABASE', 0),
    ],
];
