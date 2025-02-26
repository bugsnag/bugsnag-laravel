<?php

namespace App\Http\Middleware;

use Closure;
use Exception;

class UnhandledMiddlewareEx
{
    public function handle($request, Closure $next)
    {
        throw new Exception('Unhandled middleware exception');
        return $next($request);
    }
}
