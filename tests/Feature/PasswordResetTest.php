<?php
declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class PasswordResetTest extends TestCase
{
    public function testResetThePasswordByEmail(): void
    {
        $this->createUser();

        $this->post('/forgot-password', ['email' => 'ana@example.test'])->assertRedirect('/login');
        $this->get('/login')->assertSee('If that address has an account, we sent it a link');

        $mail = $this->sentMail();
        $this->assertCount(1, $mail);
        $this->assertSame(['ana@example.test'], $mail[0]['to']);
        $this->assertSame('Reset your password', $mail[0]['subject']);
        $this->assertSame(1, preg_match('~/reset-password\?token=([0-9a-f]+)~', $mail[0]['text'], $m), 'the email has the link');
        $link = '/reset-password?token=' . $m[1];

        $this->get($link)->assertOk()->assertHeader('Referrer-Policy', 'no-referrer')->assertSee('Type it again');
        $this->post('/reset-password', [
            'token'                 => $m[1],
            'password'              => 'a brand new password',
            'password_confirmation' => 'a brand new password',
        ])->assertRedirect('/login');

        $this->get('/login')->assertSee('Your password was changed.');
        $this->login('ana@example.test', 'correct horse battery')->assertRedirect('/login');
        $this->login('ana@example.test', 'a brand new password')->assertRedirect('/');

        $this->get($link)->assertSee('This reset link is invalid, used or expired.');
    }

    public function testUnknownAddressGetsTheSameAnswerAndNoEmail(): void
    {
        $this->post('/forgot-password', ['email' => 'nobody@example.test'])->assertRedirect('/login');
        $this->get('/login')->assertSee('If that address has an account, we sent it a link');

        $this->assertSame([], $this->sentMail());
    }

    public function testATooShortPasswordKeepsTheLinkWorking(): void
    {
        $this->createUser();
        $this->post('/forgot-password', ['email' => 'ana@example.test']);
        preg_match('~token=([0-9a-f]+)~', $this->sentMail()[0]['text'], $m);
        $link = '/reset-password?token=' . $m[1];

        $this->post('/reset-password', ['token' => $m[1], 'password' => 'short', 'password_confirmation' => 'short'])
            ->assertRedirect($link);

        $this->get($link)->assertSee('at least 10 characters')->assertSee('Type it again');
    }
}
