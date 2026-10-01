<?php
declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\DashboardController;

return [
    'GET /'        => [DashboardController::class, 'index', ['auth']],
    'GET /login'   => [AuthController::class, 'showLogin'],
    'POST /login'  => [AuthController::class, 'login', ['csrf']],
    'POST /logout' => [AuthController::class, 'logout', ['auth', 'csrf']],
];
