<?php
declare(strict_types=1);

namespace App\Commands;

use Whitesmoke\Console\Command;
use Whitesmoke\Console\Input;
use Whitesmoke\Console\Output;
use Whitesmoke\Database\Migrations\Migrator;

final class SetupCommand implements Command
{
    public function name(): string
    {
        return 'setup';
    }

    public function description(): string
    {
        return 'Run migrations and create an admin user';
    }

    public function usage(): string
    {
        return 'setup [--email=admin@example.com] [--password=secret] [--name=Admin]';
    }

    public function handle(Input $input, Output $output): int
    {
        (new Migrator(db(), BASE_PATH . '/database/migrations'))
            ->migrate(fn (string $line) => $output->line($line));

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
