<?php
declare(strict_types=1);

return [
    // Login throttling. After the limit is reached, logins are refused for lock_seconds.
    'throttle' => [
        'per_account'  => 5,    // failed attempts for one email from one IP
        'per_ip'       => 20,   // failed attempts from one IP, any emails (raise for shared office IPs)
        'lock_seconds' => 900,  // 15 minutes
    ],

    // "Remember me" on the login form: stay logged in on that device for this many days
    // (1 to 365). Logging out there, or changing the password, ends it.
    'remember' => [
        'days' => 30,
    ],

    // Changing the password while logged in: wrong current passwords per user.
    'change_password' => [
        'attempts'     => 5,
        'lock_seconds' => 900,
    ],

    // Password reset by email.
    'reset' => [
        'lifetime'     => 3600, // seconds a reset link works (60 to 86400)
        'per_email'    => 3,    // reset emails to one address per lock period
        'per_ip'       => 10,   // reset requests from one IP per lock period
        'lock_seconds' => 3600,
    ],
];
