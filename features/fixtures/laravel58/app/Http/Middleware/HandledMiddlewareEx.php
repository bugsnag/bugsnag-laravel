<?php

namespace App\Http\Middleware;

use Bugsnag\BugsnagLaravel\Facades\Bugsnag;
use Closure;
use Exception;

class HandledMiddlewareEx
{
    public function handle($request, Closure $next)
    {
        Bugsnag::notifyException(new Exception('Handled middleware exception'));
        $next($request);
    }
}
