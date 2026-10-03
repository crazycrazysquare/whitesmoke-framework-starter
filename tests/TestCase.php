<?php
declare(strict_types=1);

namespace Tests;

use Whitesmoke\Testing\AppTestCase;

/**
 * Base class for this app's tests. Each test starts with an empty database (migrated),
 * no cookies and no mail; see Whitesmoke\Testing\AppTestCase for the helpers.
 */
abstract class TestCase extends AppTestCase
{
    /** Create a user and return its row. */
    protected function createUser(string $email = 'ana@example.test', string $password = 'correct horse battery', string $name = 'Ana'): array
    {
        table('users')->insert([
            'name'     => $name,
            'email'    => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
        ]);

        return table('users')->where('email', '=', $email)->first();
    }
}
