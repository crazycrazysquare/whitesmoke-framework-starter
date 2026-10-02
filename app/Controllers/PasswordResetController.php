<?php
declare(strict_types=1);

namespace App\Controllers;

use RuntimeException;
use Throwable;
use Whitesmoke\Auth\PasswordResets;
use Whitesmoke\Http\Request;
use Whitesmoke\Http\Response;
use Whitesmoke\Mail\Message;
use Whitesmoke\Security\Throttle;

/**
 * "Forgot password" by email. The answer is the same whether or not the address
 * has an account, and the email is sent after the response, so neither the
 * message nor the timing tells anyone which addresses are registered.
 */
final class PasswordResetController
{
    public function showRequest(Request $request): Response
    {
        return Response::html(view()->render('auth/forgot', ['title' => 'Forgot password'], 'layouts/app'));
    }

    public function sendLink(Request $request): Response
    {
        $email  = strtolower(trim($request->post('email', '') ?? ''));
        $limits = (require BASE_PATH . '/config/auth.php')['reset'];

        if (validate(['email' => $email], ['email' => 'required|email|max:254'])->fails()) {
            session()->flash('error', 'Enter a valid email address.');
            session()->flash('old', ['email' => $email]);
            return Response::redirect('/forgot-password');
        }

        // Check the configuration on every request, not only for registered addresses,
        // so a broken setup fails loudly and the same way for everyone.
        $base = self::appUrl();
        mailer();

        $throttle = new Throttle();

        if ($throttle->hit('reset-ip:' . hash('sha256', $request->ip()), $limits['per_ip'], $limits['lock_seconds']) > $limits['per_ip']) {
            logger()->warning('Password reset refused over the IP limit', ['ip' => $request->ip()]);
            session()->flash('error', 'Too many reset requests. Try again later.');
            return Response::redirect('/forgot-password');
        }

        $minutes = intdiv((int) $limits['lifetime'], 60);

        if ($throttle->hit('reset:' . hash('sha256', $email), $limits['per_email'], $limits['lock_seconds']) > $limits['per_email']) {
            logger()->warning('Password reset email skipped over the address limit', ['ip' => $request->ip()]);
        } elseif ($user = table('users')->where('email', '=', $email)->first()) {
            $token = (new PasswordResets((int) $limits['lifetime']))->create((int) $user['id']);
            $link  = $base . '/reset-password?token=' . $token;
            $mail  = new Message($email, 'Reset your password', implode("\n", [
                'Hello ' . $user['name'] . ',',
                '',
                'Someone asked to reset the password for your account at ' . parse_url($link, PHP_URL_HOST) . '.',
                "To choose a new password, open this link within {$minutes} minutes:",
                '',
                $link,
                '',
                'If you did not ask for this, ignore this email. Your password stays the same.',
            ]));

            // After the response, so a registered address does not answer more slowly.
            register_shutdown_function(static function () use ($mail, $user): void {
                try {
                    mailer()->send($mail);
                    logger()->info('Password reset email sent', ['user_id' => $user['id']]);
                } catch (Throwable $e) {
                    logger()->error('Password reset email failed: ' . $e->getMessage(), ['user_id' => $user['id']]);
                }
            });
        }

        session()->flash('success', "If that address has an account, we sent it a link to choose a new password. The link works for {$minutes} minutes.");

        return Response::redirect('/login');
    }

    public function showReset(Request $request): Response
    {
        $token = $request->get('token', '') ?? '';
        $valid = (new PasswordResets(self::lifetime()))->check($token) !== null;

        // The token is in this page's URL: never send it on as a Referer.
        return Response::html(view()->render('auth/reset', ['title' => 'Choose a new password', 'token' => $token, 'valid' => $valid], 'layouts/app'))
            ->header('Referrer-Policy', 'no-referrer');
    }

    public function reset(Request $request): Response
    {
        $token = $request->post('token', '') ?? '';
        $back  = '/reset-password?token=' . rawurlencode($token);

        $input = validate(
            ['password' => $request->post('password', '') ?? '', 'password_confirmation' => $request->post('password_confirmation', '') ?? ''],
            ['password' => 'required|min:10|max:1024|same:password_confirmation']
        );

        if ($input->fails()) {
            session()->flash('error', 'Choose a password of at least 10 characters, and type it the same way twice.');
            return Response::redirect($back);
        }

        // Validate first, so a rejected password does not use up the link.
        $userId = (new PasswordResets(self::lifetime()))->consume($token);

        if ($userId === null) {
            session()->flash('error', 'This reset link is invalid, used or expired. Request a new one.');
            return Response::redirect('/forgot-password');
        }

        table('users')->where('id', '=', $userId)->update([
            'password'   => password_hash($request->post('password', '') ?? '', PASSWORD_DEFAULT),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        (new PasswordResets(self::lifetime()))->clear($userId);
        AuthController::rememberMe()->clear($userId);   // also revoked by the new password; this removes the rows

        session()->regenerate();
        logger()->info('Password reset', ['user_id' => $userId, 'ip' => $request->ip()]);
        session()->flash('success', 'Your password was changed. Log in with the new password.');

        return Response::redirect('/login');
    }

    private static function lifetime(): int
    {
        return (int) (require BASE_PATH . '/config/auth.php')['reset']['lifetime'];
    }

    /** APP_URL without a trailing slash; fails closed when it is missing or not a plain site address. */
    private static function appUrl(): string
    {
        $url   = rtrim((string) (require BASE_PATH . '/config/app.php')['url'], '/');
        $parts = parse_url($url);

        if (!is_array($parts) || !in_array($parts['scheme'] ?? '', ['http', 'https'], true) || ($parts['host'] ?? '') === ''
            || isset($parts['user']) || isset($parts['query']) || isset($parts['fragment']) || preg_match('~[\s"<>]~', $url)) {
            throw new RuntimeException('Set APP_URL to the site address, such as https://app.example.com, to send reset links');
        }

        return $url;
    }
}
