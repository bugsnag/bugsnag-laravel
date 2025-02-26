<?php

namespace App\Providers;

use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Bugsnag\BugsnagLaravel\Facades\Bugsnag;
use Illuminate\Queue\Events\JobExceptionOccurred;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (!getenv('BUGSNAG_USE_CUSTOM_GUZZLE')) {
            return;
        }

        $this->app->singleton('bugsnag.guzzle', function ($app) {
            $handler = \GuzzleHttp\HandlerStack::create();
            $handler->push(\GuzzleHttp\Middleware::mapRequest(function ($request) {
                return $request->withHeader('X-Custom-Guzzle', 'yes');
            }));

            return new \GuzzleHttp\Client(['handler' => $handler]);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Bugsnag::leaveBreadcrumb(__METHOD__);

        Queue::before(function (JobProcessing $event) {
            Bugsnag::leaveBreadcrumb('before');
        });

        Queue::after(function (JobProcessed $event) {
            Bugsnag::leaveBreadcrumb('after');
        });

        Queue::exceptionOccurred(function (JobExceptionOccurred $event) {
            Bugsnag::leaveBreadcrumb('exceptionOccurred');
        });
    }
}
