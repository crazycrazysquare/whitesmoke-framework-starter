<?php
declare(strict_types=1);

return [
    // Login throttling. After the limit is reached, logins are refused for lock_seconds.
    'throttle' => [
        'per_account'  => 5,    // failed attempts for one email from one IP
        'per_ip'       => 20,   // failed attempts from one IP, any emails (raise for shared office IPs)
        'lock_seconds' => 900,  // 15 minutes
    ],
];
