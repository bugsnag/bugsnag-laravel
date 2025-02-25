<?php

use Illuminate\Foundation\Application;
use Bugsnag\BugsnagLaravel\OomBootstrapper;
use Bugsnag\BugsnagLaravel\Facades\Bugsnag;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

(new OomBootstrapper())->bootstrap();

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'unMidEx' => \App\Http\Middleware\UnhandledMiddlewareEx::class,
            'unMidErr' => \App\Http\Middleware\UnhandledMiddlewareErr::class,
            'hanMidEx' => \App\Http\Middleware\HandledMiddlewareEx::class,
            'hanMidErr' => \App\Http\Middleware\HandledMiddlewareErr::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->booted(function(){
        Bugsnag::setMemoryLimitIncrease($value = 6 * 1024 * 1024);
    })
    ->create();
