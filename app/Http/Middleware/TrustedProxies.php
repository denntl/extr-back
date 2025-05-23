<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies;

class TrustedProxies extends TrustProxies
{
    protected $proxies = '*';
}
