<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Controllers\AuthController;
use Whitesmoke\Auth\SessionUser;
use Whitesmoke\Http\Request;
use Whitesmoke\Http\Response;

/**
 * Lets logged-in users through. A session from before a password change, or of a
 * deleted user, is ended here, so a password reset logs out every other device.
 * Without a session, a valid "remember me" cookie logs the user back in.
 */
final class Auth
{
    public function handle(Request $request): ?Response
    {
        $wasLoggedIn = session()->get('user_id') !== null;
        $sessions    = new SessionUser();

        if ($sessions->check() !== null) {
            return null;
        }

        $user = AuthController::rememberMe()->user($request);

        if ($user !== null) {
            $sessions->remember($user);
            session()->put('user_name', $user['name']);
            return null;
        }

        if ($wasLoggedIn) {
            session()->flash('error', 'Your session has ended. Please log in again.');
        }

        return Response::redirect('/login');
    }
}
