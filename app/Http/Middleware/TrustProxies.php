<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * Shared hosting terminates TLS at a proxy in front of PHP, so without
     * this Laravel sees a plain HTTP request and asset() emits http:// URLs on
     * an https:// page. Browsers then block those as mixed content and the
     * stylesheets, images and icon font silently fail to load.
     *
     * The app is only reachable through the host's proxy, so trusting the
     * forwarded headers is safe here.
     *
     * @var array<int, string>|string|null
     */
    protected $proxies = '*';

    /**
     * The headers that should be used to detect proxies.
     *
     * @var int
     */
    protected $headers =
        Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_HOST |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO |
        Request::HEADER_X_FORWARDED_AWS_ELB;
}
