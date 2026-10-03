<?php
declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class ChangePasswordTest extends TestCase
{
    public function testChangeThePassword(): void
    {
        $this->createUser();
        $this->login('ana@example.test', 'correct horse battery');

        $this->get('/account/password')->assertOk()->assertSee('Current password');
        $this->post('/account/password', [
            'current_password'      => 'correct horse battery',
            'password'              => 'a brand new password',
            'password_confirmation' => 'a brand new password',
        ])->assertRedirect('/');

        $this->get('/')->assertSee('Your password was changed.')->assertSee('Welcome, Ana.');

        $this->post('/logout');
        $this->login('ana@example.test', 'correct horse battery')->assertRedirect('/login');
        $this->login('ana@example.test', 'a brand new password')->assertRedirect('/');
    }

    public function testTheCurrentPasswordIsRequired(): void
    {
        $this->createUser();
        $this->login('ana@example.test', 'correct horse battery');

        $this->post('/account/password', [
            'current_password'      => 'not my password',
            'password'              => 'a brand new password',
            'password_confirmation' => 'a brand new password',
        ])->assertRedirect('/account/password');

        $this->get('/account/password')->assertSee('Your current password is not correct.');
        $this->assertTrue(password_verify('correct horse battery', table('users')->where('email', '=', 'ana@example.test')->first()['password']));
    }

    public function testGuestsCannotChangeAPassword(): void
    {
        $this->get('/account/password')->assertRedirect('/login');
    }
}
