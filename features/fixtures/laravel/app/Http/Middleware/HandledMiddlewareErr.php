<?php

namespace App\Http\Middleware;

use Bugsnag\BugsnagLaravel\Facades\Bugsnag;
use Closure;

class HandledMiddlewareErr
{
    public function handle($request, Closure $next)
    {
        Bugsnag::notifyError('Handled middleware error', 'This is a handled error');
        $next($request);
    }
}
