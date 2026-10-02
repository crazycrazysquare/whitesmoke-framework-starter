<?php
declare(strict_types=1);

return [
    // smtp: send through MAIL_HOST. log: write each email to storage/logs/mail-*.log
    // instead (development only).
    'driver'       => (string) env('MAIL_DRIVER', ''),
    'host'         => (string) env('MAIL_HOST', ''),
    'port'         => (int) env('MAIL_PORT', 587),
    'encryption'   => (string) env('MAIL_ENCRYPTION', 'tls'),  // tls, ssl, or none (only to this machine)
    'username'     => (string) env('MAIL_USERNAME', ''),
    'password'     => (string) env('MAIL_PASSWORD', ''),
    'from_address' => (string) env('MAIL_FROM_ADDRESS', ''),
    'from_name'    => (string) env('MAIL_FROM_NAME', ''),
    'ehlo'         => parse_url((string) env('APP_URL', ''), PHP_URL_HOST) ?: 'localhost',
    'timeout'      => 10,
];
