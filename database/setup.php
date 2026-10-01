<?php
declare(strict_types=1);

/**
 * Creates the framework tables and an admin user.
 *
 * Usage: php database/setup.php [email] [password]
 * Without a password, a random one is generated and printed once.
 */

require __DIR__ . '/../vendor/autoload.php';

new Whitesmoke\Foundation\Application(dirname(__DIR__));

$driver = db()->getAttribute(PDO::ATTR_DRIVER_NAME);

$id = match ($driver) {
    'sqlite' => 'INTEGER PRIMARY KEY AUTOINCREMENT',
    'mysql'  => 'BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
    'pgsql'  => 'BIGSERIAL PRIMARY KEY',
    'sqlsrv' => 'BIGINT IDENTITY(1,1) PRIMARY KEY',
    default  => throw new RuntimeException("Unsupported driver: {$driver}"),
};

$timestamp = match ($driver) {
    'sqlsrv' => 'DATETIME2 NOT NULL DEFAULT SYSUTCDATETIME()',
    default  => 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
};

$tables = [
    'users' => "
        id {$id},
        name VARCHAR(100) NOT NULL,
        email VARCHAR(254) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at {$timestamp}
    ",
    'throttle' => "
        id {$id},
        throttle_key VARCHAR(100) NOT NULL UNIQUE,
        attempts INTEGER NOT NULL DEFAULT 0,
        window_ends BIGINT NOT NULL,
        locked_until BIGINT NOT NULL DEFAULT 0
    ",
];

foreach ($tables as $name => $columns) {
    $sql = $driver === 'sqlsrv'
        ? "IF OBJECT_ID('{$name}', 'U') IS NULL CREATE TABLE {$name} ({$columns})"
        : "CREATE TABLE IF NOT EXISTS {$name} ({$columns})";

    db()->exec($sql);
    echo "Table ready: {$name}\n";
}

$email    = strtolower(trim($argv[1] ?? 'admin@whitesmoke.test'));
$password = $argv[2] ?? null;
$generated = $password === null;

if ($generated) {
    $password = rtrim(strtr(base64_encode(random_bytes(12)), '+/', '-_'), '=');
}

$input = validate(['email' => $email, 'password' => $password], [
    'email'    => 'required|email|max:254',
    'password' => 'required|min:10|max:1024',
]);

if ($input->fails()) {
    fwrite(STDERR, implode(PHP_EOL, $input->errors()) . PHP_EOL);
    exit(1);
}

if (table('users')->where('email', '=', $email)->first() !== null) {
    echo "User {$email} already exists, skipped.\n";
    exit(0);
}

table('users')->insert([
    'name'     => 'Admin',
    'email'    => $email,
    'password' => password_hash($password, PASSWORD_DEFAULT),
]);

echo "Admin created: {$email}\n";
if ($generated) {
    echo "Password: {$password}\n(shown once, store it now)\n";
}
