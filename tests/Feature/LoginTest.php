<?php
declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class LoginTest extends TestCase
{
    public function testGuestsAreSentToTheLoginPage(): void
    {
        $this->get('/')->assertRedirect('/login');
        $this->get('/login')->assertOk()->assertSee('Login')->assertSee('Forgot your password?');
    }

    public function testLoginAndLogout(): void
    {
        $this->createUser();

        $this->login('ana@example.test', 'correct horse battery')->assertRedirect('/');
        $this->get('/')->assertOk()->assertSee('Welcome, Ana.');

        $this->post('/logout')->assertRedirect('/login');
        $this->get('/login')->assertSee('You have been logged out.');
        $this->get('/')->assertRedirect('/login');
    }

    public function testWrongPasswordIsRefused(): void
    {
        $this->createUser();

        $this->login('ana@example.test', 'wrong password')->assertRedirect('/login');
        $this->get('/login')->assertSee('Invalid email or password.');
        $this->get('/')->assertRedirect('/login');
    }

    public function testLoginNeedsTheCsrfToken(): void
    {
        $this->createUser();

        // request() sends exactly what it is given: here, no token.
        $this->request('POST', '/login', ['email' => 'ana@example.test', 'password' => 'correct horse battery'])->assertStatus(403);
        $this->get('/')->assertRedirect('/login');
    }

    public function testRepeatedFailuresLockTheAccount(): void
    {
        $this->createUser();

        for ($i = 0; $i < 5; $i++) {
            $this->login('ana@example.test', 'wrong password');
        }

        $this->login('ana@example.test', 'correct horse battery')->assertRedirect('/login');
        $this->get('/login')->assertSee('Too many login attempts');
        $this->get('/')->assertRedirect('/login');
    }

    public function testRememberMeOutlivesTheSession(): void
    {
        $this->createUser();

        $this->login('ana@example.test', 'correct horse battery', remember: true);
        $this->forgetCookies('ws_session');   // as after closing the browser

        $this->get('/')->assertOk()->assertSee('Welcome, Ana.');
    }

    public function testWithoutRememberMeClosingTheBrowserLogsOut(): void
    {
        $this->createUser();

        $this->login('ana@example.test', 'correct horse battery');
        $this->forgetCookies('ws_session');

        $this->get('/')->assertRedirect('/login');
    }
}
