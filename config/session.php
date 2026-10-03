<?php
declare(strict_types=1);

return [
    'name'     => 'ws_session',
    'path'     => storage_path('sessions'),
    'idle'     => 1800,
    'absolute' => 28800,
    'secure'   => env('SESSION_SECURE', true) !== false,
    'samesite' => 'Lax',
];
