<?php
declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\PasswordResetController;

return [
    'GET /'                 => [DashboardController::class, 'index', ['auth']],
    'GET /login'            => [AuthController::class, 'showLogin'],
    'POST /login'           => [AuthController::class, 'login', ['csrf']],
    'POST /logout'          => [AuthController::class, 'logout', ['auth', 'csrf']],
    'GET /forgot-password'  => [PasswordResetController::class, 'showRequest'],
    'POST /forgot-password' => [PasswordResetController::class, 'sendLink', ['csrf']],
    'GET /reset-password'   => [PasswordResetController::class, 'showReset'],
    'POST /reset-password'  => [PasswordResetController::class, 'reset', ['csrf']],
];
