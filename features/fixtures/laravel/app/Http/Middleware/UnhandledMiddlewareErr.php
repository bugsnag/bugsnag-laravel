<?php

namespace App\Http\Middleware;

use Closure;

class UnhandledMiddlewareErr
{
    public function handle($request, Closure $next)
    {
        foo();
        $next($request);
    }
}
