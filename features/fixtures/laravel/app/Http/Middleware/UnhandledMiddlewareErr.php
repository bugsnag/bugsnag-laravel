<?php

namespace App\Http\Middleware;

use Exception;
use Closure;

class UnhandledMiddlewareErr
{
    public function handle($request, Closure $next)
    {
        foo();
        $next($request);
    }
}