<?php
declare(strict_types=1);

namespace App\Middleware;

use Whitesmoke\Auth\SessionUser;
use Whitesmoke\Http\Request;
use Whitesmoke\Http\Response;

/**
 * Lets logged-in users through. A session from before a password change, or of a
 * deleted user, is ended here, so a password reset logs out every other device.
 */
final class Auth
{
    public function handle(Request $request): ?Response
    {
        $wasLoggedIn = session()->get('user_id') !== null;

        if ((new SessionUser())->check() === null) {
            if ($wasLoggedIn) {
                session()->flash('error', 'Your session has ended. Please log in again.');
            }
            return Response::redirect('/login');
        }

        return null;
    }
}
