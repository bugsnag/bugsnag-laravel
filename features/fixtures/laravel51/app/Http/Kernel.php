<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * @var array
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \App\Http\Middleware\EncryptCookies::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \App\Http\Middleware\VerifyCsrfToken::class,
    ];

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'unMidEx' => \App\Http\Middleware\UnhandledMiddlewareEx::class,
        'unMidErr' => \App\Http\Middleware\UnhandledMiddlewareErr::class,
        'hanMidEx' => \App\Http\Middleware\HandledMiddlewareEx::class,
        'hanMidErr' => \App\Http\Middleware\HandledMiddlewareErr::class,
    ];

    protected function bootstrappers()
    {
        if (!getenv('BUGSNAG_REGISTER_OOM_BOOTSTRAPPER')) {
            return parent::bootstrappers();
        }

        return array_merge(
            [\Bugsnag\BugsnagLaravel\OomBootstrapper::class],
            parent::bootstrappers(),
        );
    }
}
