<?php
declare(strict_types=1);

return [
    'name'     => 'ws_session',
    'path'     => BASE_PATH . '/storage/sessions',
    'idle'     => 1800,
    'absolute' => 28800,
    'secure'   => getenv('SESSION_SECURE') !== 'false',
    'samesite' => 'Lax',
];
