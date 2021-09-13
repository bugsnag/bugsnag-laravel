<?php

namespace App\Http\Middleware;

use Bugsnag\BugsnagLaravel\Facades\Bugsnag;
use Closure;
use Exception;

class HandledMiddlewareException
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
        Bugsnag::notifyException(new Exception('Handled middleware exception'));

        return $next($request);
    }
}
