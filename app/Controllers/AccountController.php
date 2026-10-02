<?php
declare(strict_types=1);

namespace App\Controllers;

use Whitesmoke\Auth\SessionUser;
use Whitesmoke\Http\Request;
use Whitesmoke\Http\Response;
use Whitesmoke\Security\Throttle;

/**
 * Change the password while logged in. Needs the current password (rate limited),
 * so a session left open on someone else's screen cannot be used to take over the
 * account. Afterwards every other device and "remember me" is logged out; this one
 * stays logged in.
 */
final class AccountController
{
    public function showPassword(Request $request): Response
    {
        return Response::html(view()->render('account/password', ['title' => 'Change password'], 'layouts/app'));
    }

    public function changePassword(Request $request): Response
    {
        $user = (new SessionUser())->check();

        if ($user === null) {
            return Response::redirect('/login');
        }

        $limits   = (require BASE_PATH . '/config/auth.php')['change_password'];
        $throttle = new Throttle();
        $key      = 'change-password:' . $user['id'];

        if ($throttle->tooMany($key)) {
            $minutes = max(1, (int) ceil($throttle->availableIn($key) / 60));
            session()->flash('error', "Too many wrong passwords. Try again in {$minutes} minute" . ($minutes === 1 ? '' : 's') . '.');
            return Response::redirect('/account/password');
        }

        $current  = $request->post('current_password', '') ?? '';
        $password = $request->post('password', '') ?? '';

        if (!password_verify($current, $user['password'])) {
            $throttle->hit($key, $limits['attempts'], $limits['lock_seconds']);
            logger()->warning('Password change refused: wrong current password', ['user_id' => $user['id'], 'ip' => $request->ip()]);
            session()->flash('error', 'Your current password is not correct.');
            return Response::redirect('/account/password');
        }

        $input = validate(
            ['password' => $password, 'password_confirmation' => $request->post('password_confirmation', '') ?? ''],
            ['password' => 'required|min:10|max:1024|same:password_confirmation']
        );

        if ($input->fails()) {
            session()->flash('error', 'Choose a new password of at least 10 characters, and type it the same way twice.');
            return Response::redirect('/account/password');
        }

        if (password_verify($password, $user['password'])) {
            session()->flash('error', 'The new password must be different from the current one.');
            return Response::redirect('/account/password');
        }

        $user['password'] = password_hash($password, PASSWORD_DEFAULT);
        table('users')->where('id', '=', $user['id'])->update(['password' => $user['password'], 'updated_at' => date('Y-m-d H:i:s')]);
        $throttle->clear($key);

        // Other sessions and remembered logins no longer match the password. Keep this one:
        // a new session id with the new fingerprint, and a new "remember me" if it had one.
        $remember   = AuthController::rememberMe();
        $remembered = $request->cookie($remember->cookieName()) !== null;
        $remember->clear((int) $user['id']);
        (new SessionUser())->remember($user);
        if ($remembered) {
            $remember->issue($user);
        }

        logger()->info('Password changed', ['user_id' => $user['id'], 'ip' => $request->ip()]);
        session()->flash('success', 'Your password was changed. Other devices were logged out.');

        return Response::redirect('/');
    }
}
