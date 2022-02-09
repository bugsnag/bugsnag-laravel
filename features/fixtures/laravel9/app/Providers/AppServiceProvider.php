<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
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
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
