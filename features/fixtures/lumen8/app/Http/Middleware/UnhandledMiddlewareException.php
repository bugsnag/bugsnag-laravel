<?php

namespace App\Http\Middleware;

use Closure;
use Exception;

class UnhandledMiddlewareException
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        throw new Exception('Unhandled middleware exception');

        return $next($request);
    }
}
