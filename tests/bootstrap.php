<?php
declare(strict_types=1);

/*
 * Tests never read .env. Tests that extend Tests\TestCase run the app with a temporary
 * SQLite database and storage folder. Anywhere else, db() is an empty in-memory SQLite
 * database, so no test can reach your development data.
 */

require dirname(__DIR__) . '/vendor/autoload.php';

define('BASE_PATH', dirname(__DIR__));

foreach (['DB_CONNECTION' => 'sqlite', 'DB_DATABASE' => ':memory:'] as $key => $value) {
    putenv("{$key}={$value}");
    $_ENV[$key] = $_SERVER[$key] = $value;
}
