<?php
declare(strict_types=1);

return [
    'path'  => BASE_PATH . '/storage/logs',       // daily files: whitesmoke-YYYY-MM-DD.log
    'level' => getenv('LOG_LEVEL') ?: 'info',     // debug, info, warning, error
    'days'  => 14,                                // keep this many days (0 = forever)
];
