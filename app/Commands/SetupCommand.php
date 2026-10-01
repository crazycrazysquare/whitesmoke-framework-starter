<?php
declare(strict_types=1);

namespace App\Commands;

use PDO;
use RuntimeException;
use Whitesmoke\Console\Command;
use Whitesmoke\Console\Input;
use Whitesmoke\Console\Output;

final class SetupCommand implements Command
{
    public function name(): string
    {
        return 'setup';
    }

    public function description(): string
    {
        return 'Create the framework tables and an admin user';
    }

    public function usage(): string
    {
        return 'setup [--email=admin@example.com] [--password=secret] [--name=Admin]';
    }

    public function handle(Input $input, Output $output): int
    {
        $driver = db()->getAttribute(PDO::ATTR_DRIVER_NAME);

        $id = match ($driver) {
            'sqlite' => 'INTEGER PRIMARY KEY AUTOINCREMENT',
            'mysql'  => 'BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
            'pgsql'  => 'BIGSERIAL PRIMARY KEY',
            'sqlsrv' => 'BIGINT IDENTITY(1,1) PRIMARY KEY',
            default  => throw new RuntimeException("Unsupported driver: {$driver}"),
        };

        $timestamp = $driver === 'sqlsrv'
            ? 'DATETIME2 NOT NULL DEFAULT SYSUTCDATETIME()'
            : 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP';

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

        foreach ($tables as $table => $columns) {
            db()->exec($driver === 'sqlsrv'
                ? "IF OBJECT_ID('{$table}', 'U') IS NULL CREATE TABLE {$table} ({$columns})"
                : "CREATE TABLE IF NOT EXISTS {$table} ({$columns})");
            $output->line("Table ready: {$table}");
        }

        $email     = strtolower(trim((string) $input->option('email', 'admin@whitesmoke.test')));
        $name      = trim((string) $input->option('name', 'Admin'));
        $password  = $input->option('password');
        $generated = !is_string($password);

        if ($generated) {
            $password = rtrim(strtr(base64_encode(random_bytes(12)), '+/', '-_'), '=');
        }

        $check = validate(['email' => $email, 'name' => $name, 'password' => $password], [
            'email'    => 'required|email|max:254',
            'name'     => 'required|max:100',
            'password' => 'required|min:10|max:1024',
        ]);

        if ($check->fails()) {
            foreach ($check->errors() as $message) {
                $output->error($message);
            }
            return 1;
        }

        if (table('users')->where('email', '=', $email)->first() !== null) {
            $output->comment("User {$email} already exists, skipped.");
            return 0;
        }

        table('users')->insert([
            'name'     => $name,
            'email'    => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
        ]);

        $output->info("Admin created: {$email}");

        if ($generated) {
            $output->line("Password: {$password}");
            $output->comment('Shown once. Store it now.');
        }

        return 0;
    }
}
