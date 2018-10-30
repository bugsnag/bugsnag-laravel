<?php

namespace App\Http\Middleware;
use Bugsnag\BugsnagLaravel\Facades\Bugsnag;

use Exception;
use Closure;

class HandledMiddlewareEx
{
    public function handle($request, Closure $next)
    {
        Bugsnag::notifyException(new Exception("Handled middleware exception"));
        $next($request);
    }
}