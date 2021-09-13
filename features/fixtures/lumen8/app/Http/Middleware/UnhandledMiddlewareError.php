<?php

namespace App\Http\Middleware;

use Closure;

class UnhandledMiddlewareError
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
        foo();

        return $next($request);
    }
}
