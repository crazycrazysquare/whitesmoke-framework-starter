<?php
declare(strict_types=1);

return [
    // The site's public address, e.g. https://app.example.com. Used to build links
    // in emails; never taken from the request, whose Host header can be forged.
    'url' => (string) env('APP_URL', ''),
];
