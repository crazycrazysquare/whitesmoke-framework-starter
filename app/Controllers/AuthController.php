<?php
declare(strict_types=1);

namespace App\Controllers;

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

        $throttle = new Throttle();
        $userKey  = 'login:' . hash('sha256', $email . '|' . $request->ip());
        $ipKey    = 'login-ip:' . hash('sha256', $request->ip());

        $wait = max($throttle->availableIn($userKey), $throttle->availableIn($ipKey));

        if ($wait > 0) {
            $minutes = (int) ceil($wait / 60);
            session()->flash('error', "Too many login attempts. Try again in {$minutes} minute" . ($minutes === 1 ? '' : 's') . '.');
            session()->flash('old', ['email' => $email]);
            return Response::redirect('/login');
        }

        $input = validate(['email' => $email, 'password' => $password], [
            'email'    => 'required|email|max:254',
            'password' => 'required|max:1024',
        ]);

        $user  = $input->fails() ? null : table('users')->where('email', '=', $email)->first();
        $valid = password_verify($password, $user['password'] ?? self::DUMMY_HASH) && $user !== null;

        if (!$valid) {
            $throttle->hit($userKey, 5, 900);
            $throttle->hit($ipKey, 20, 900);

            session()->flash('error', 'Invalid email or password.');
            session()->flash('old', ['email' => $email]);
            return Response::redirect('/login');
        }

        $throttle->clear($userKey);

        if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
            table('users')->where('id', '=', $user['id'])->update(['password' => password_hash($password, PASSWORD_DEFAULT)]);
        }

        session()->regenerate();
        session()->put('user_id', $user['id']);
        session()->put('user_name', $user['name']);

        return Response::redirect('/');
    }

    public function logout(Request $request): Response
    {
        session()->invalidate();
        session()->flash('success', 'You have been logged out.');

        return Response::redirect('/login');
    }
}
