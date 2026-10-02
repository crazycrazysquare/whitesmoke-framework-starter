<?php
declare(strict_types=1);

namespace App\Controllers;

use Whitesmoke\Auth\RememberMe;
use Whitesmoke\Auth\SessionUser;
use Whitesmoke\Http\Request;
use Whitesmoke\Http\Response;
use Whitesmoke\Security\Throttle;

final class AuthController
{
    private const DUMMY_HASH = '$2y$10$ct1aUJGIlEDowmU24NWqseDsnJmDbsLRPqK1.TkgiMlDWCJX83XIC';

    public function showLogin(Request $request): Response
    {
        if (session()->get('user_id') !== null) {
            return Response::redirect('/');
        }

        return Response::html(view()->render('auth/login', ['title' => 'Login'], 'layouts/app'));
    }

    public function login(Request $request): Response
    {
        $email    = strtolower(trim($request->post('email', '')));
        $password = $request->post('password', '');

        $limits   = (require BASE_PATH . '/config/auth.php')['throttle'];
        $throttle = new Throttle();
        $userKey  = 'login:' . hash('sha256', $email . '|' . $request->ip());
        $ipKey    = 'login-ip:' . hash('sha256', $request->ip());

        $wait = max($throttle->availableIn($userKey), $throttle->availableIn($ipKey));

        if ($wait > 0) {
            logger()->warning('Login refused while locked', ['ip' => $request->ip()]);
            return $this->locked($wait, $email);
        }

        // Reserve the attempt before checking the password, so a burst of
        // simultaneous requests cannot slip past the limit.
        if ($throttle->hit($userKey, $limits['per_account'], $limits['lock_seconds']) > $limits['per_account']) {
            logger()->warning('Login refused over the limit', ['ip' => $request->ip()]);
            return $this->locked($throttle->availableIn($userKey), $email);
        }

        $input = validate(['email' => $email, 'password' => $password], [
            'email'    => 'required|email|max:254',
            'password' => 'required|max:1024',
        ]);

        $user  = $input->fails() ? null : table('users')->where('email', '=', $email)->first();
        $valid = password_verify($password, $user['password'] ?? self::DUMMY_HASH) && $user !== null;

        if (!$valid) {
            $throttle->hit($ipKey, $limits['per_ip'], $limits['lock_seconds']);

            if ($throttle->tooMany($userKey) || $throttle->tooMany($ipKey)) {
                logger()->warning('Login locked after repeated failures', ['ip' => $request->ip()]);
            }

            session()->flash('error', 'Invalid email or password.');
            session()->flash('old', ['email' => $email]);
            return Response::redirect('/login');
        }

        $throttle->clear($userKey);

        if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
            $user['password'] = password_hash($password, PASSWORD_DEFAULT);
            table('users')->where('id', '=', $user['id'])->update(['password' => $user['password']]);
        }

        // New session id, user id and a fingerprint of the current password hash.
        (new SessionUser())->remember($user);
        session()->put('user_name', $user['name']);

        if ($request->post('remember') === '1') {
            self::rememberMe()->issue($user);
        }

        logger()->info('Login', ['user_id' => $user['id'], 'ip' => $request->ip()]);

        return Response::redirect('/');
    }

    /** "Remember me" with the settings from config/auth.php (also used by the auth middleware). */
    public static function rememberMe(): RememberMe
    {
        return new RememberMe((int) (require BASE_PATH . '/config/auth.php')['remember']['days']);
    }

    private function locked(int $seconds, string $email): Response
    {
        $minutes = max(1, (int) ceil($seconds / 60));

        session()->flash('error', "Too many login attempts. Try again in {$minutes} minute" . ($minutes === 1 ? '' : 's') . '.');
        session()->flash('old', ['email' => $email]);

        return Response::redirect('/login');
    }

    public function logout(Request $request): Response
    {
        self::rememberMe()->forget($request);
        session()->invalidate();
        session()->flash('success', 'You have been logged out.');

        return Response::redirect('/login');
    }
}
