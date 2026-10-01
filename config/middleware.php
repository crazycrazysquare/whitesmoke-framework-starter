<?php
declare(strict_types=1);

return [
    'auth' => App\Middleware\Auth::class,
    'csrf' => Whitesmoke\Middleware\Csrf::class,
];
