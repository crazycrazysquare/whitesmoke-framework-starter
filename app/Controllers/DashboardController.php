<?php
declare(strict_types=1);

namespace App\Controllers;

use PDO;
use Whitesmoke\Foundation\Application;
use Whitesmoke\Http\Request;
use Whitesmoke\Http\Response;

final class DashboardController
{
    public function index(Request $request): Response
    {
        return Response::html(view()->render('dashboard/index', [
            'title'    => 'Dashboard',
            'name'     => session()->get('user_name'),
            'version'  => Application::VERSION,
            'php'      => PHP_VERSION,
            'database' => db()->getAttribute(PDO::ATTR_DRIVER_NAME),
        ], 'layouts/app'));
    }
}
