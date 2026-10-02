<?php
declare(strict_types=1);

return [
    // file: storage/cache/data, for one server. database: the "cache" table, shared by
    // every server that uses the database (run php smoke migrate).
    'driver' => (string) env('CACHE_DRIVER', 'file'),
    'path'   => BASE_PATH . '/storage/cache/data',
    'table'  => 'cache',
    'prefix' => '',   // letters, digits, _ . : -; keeps apps that share a store apart
];
