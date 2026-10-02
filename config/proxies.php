<?php
declare(strict_types=1);

/*
 * Reverse proxies and load balancers in front of this app (Nginx, Cloudflare, a
 * cloud load balancer). Only requests arriving from these addresses may tell the
 * app the visitor's IP (X-Forwarded-For) and that the visit used HTTPS
 * (X-Forwarded-Proto). Empty: no proxy is trusted and both headers are ignored.
 *
 * A list of IP addresses or CIDR ranges, or one comma-separated string from .env.
 */
return [
    'trusted' => env('TRUSTED_PROXIES', ''),
];
