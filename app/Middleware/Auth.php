<?php
declare(strict_types=1);

namespace App\Middleware;

use Whitesmoke\Http\Request;
use Whitesmoke\Http\Response;

final class Auth
{
    public function handle(Request $request): ?Response
    {
        if (session()->get('user_id') === null) {
            return Response::redirect('/login');
        }

        return null;
    }
}
