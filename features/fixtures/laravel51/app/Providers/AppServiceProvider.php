<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Bugsnag\BugsnagLaravel\Facades\Bugsnag;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Bugsnag::leaveBreadcrumb(__METHOD__);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->alias('bugsnag.logger', \Illuminate\Contracts\Logging\Log::class);
        $this->app->alias('bugsnag.logger', \Psr\Log\LoggerInterface::class);

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
}
