<?php

namespace App\Http\Middleware;

use Exception;
use Closure;

class UnhandledMiddlewareEx
{
    public function handle($request, Closure $next)
    {
        throw new Exception("Unhandled middleware exception");
        $next($request);
    }
}