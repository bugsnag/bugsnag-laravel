<?php

namespace Bugsnag\BugsnagLaravel;

use Bugsnag\BugsnagLaravel\Middleware\AddUserData;
use Bugsnag\BugsnagLaravel\Request\LaravelResolver;
use Bugsnag\Client;
use Bugsnag\Configuration;
use GuzzleHttp\Client as Guzzle;
use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application as LumenApplication;

class BugsnagServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $source = realpath(__DIR__.'/../config/bugsnag.php');

        if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
            $this->publishes([$source => config_path('bugsnag.php')]);
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('bugsnag');
        }

        $this->mergeConfigFrom($source, 'bugsnag');

        $callback = function () {
            $this->app['bugsnag']->flush();
        };

        $this->app['queue']->after($callback);
        $this->app['queue']->stopping($callback);

        if (method_exists($this->app['queue'], 'exceptionOccurred')) {
            $this->app['queue']->exceptionOccurred($callback);
        } else {
            $this->app['queue']->looping($callback);
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('bugsnag', function (Container $app) {
            $config = $app->config->get('bugsnag');

            $configuration = new Configuration($config['api_key']);
            $resolver = new LaravelResolver($app);
            $guzzle = new Guzzle(['base_uri' => isset($config['endpoint']) ? $config['endpoint'] : Client::ENDPOINT]);

            $client = new Client($configuration, $resolver, $guzzle);

            if (!isset($config['middleware']) || $config['middleware']) {
                $client->registerDefaultMiddleware()->registerMiddleware(new AddUserData(function () use ($app) {
                    if ($user = $app->auth->user()) {
                        return $user->toArray();
                    }
                }));
            }

            $client->setStripPath($app->basePath());
            $client->setProjectRoot($app->path());
            $client->setReleaseStage($app->environment());
            $client->setNotifier([
                'name' => 'Bugsnag Laravel',
                'version' => '2.0.0',
                'url' => 'https://github.com/bugsnag/bugsnag-laravel',
            ]);

            if (isset($config['notify_release_stages']) && is_array($config['notify_release_stages'])) {
                $client->setNotifyReleaseStages($config['notify_release_stages']);
            }

            if (isset($config['filters']) && is_array($config['filters'])) {
                $client->setFilters($config['filters']);
            }

            return $client;
        });

        $this->app->singleton('bugsnag.logger', function (Container $app) {
            return new BugsnagLogger($app['bugsnag']);
        });

        $this->app->alias('bugsnag', Client::class);
        $this->app->alias('bugsnag.logger', BugsnagLogger::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['bugsnag', 'bugsnag.logger'];
    }
}
