<?php

namespace Bugsnag\BugsnagLaravel;

use Bugsnag\Breadcrumbs\Breadcrumb;
use Bugsnag\BugsnagLaravel\Request\LaravelResolver;
use Bugsnag\Callbacks\CustomUser;
use Bugsnag\Client;
use Bugsnag\Configuration;
use Exception;
use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\ClientInterface;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Queue\QueueManager;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application as LumenApplication;

class BugsnagServiceProvider extends ServiceProvider
{
    /**
     * The package version.
     *
     * @var string
     */
    const VERSION = '2.1.0';

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->call([$this, 'setupConfig']);
        $this->app->call([$this, 'setupEvents']);
        $this->app->call([$this, 'setupQueue']);
    }

    /**
     * Setup the config.
     *
     * @param \Illuminate\Contracts\Container\Container $app
     *
     * @return void
     */
    public function setupConfig(Container $app)
    {
        $source = realpath(__DIR__.'/../config/bugsnag.php');

        if ($app instanceof LaravelApplication && $app->runningInConsole()) {
            $this->publishes([$source => config_path('bugsnag.php')]);
        } elseif ($app instanceof LumenApplication) {
            $app->configure('bugsnag');
        }

        $this->mergeConfigFrom($source, 'bugsnag');
    }

    /**
     * Setup the events.
     *
     * @param \Illuminate\Contracts\Events\Dispatcher $events
     *
     * @return void
     */
    public function setupEvents(Dispatcher $events)
    {
        $events->listen('*', function () use ($events) {
            try {
                $this->app->bugsnag->leaveBreadcrumb($events->firing(), Breadcrumb::STATE_TYPE);
            } catch (Exception $e) {
                //
            }
        });
    }

    /**
     * Setup the queue.
     *
     * @param \Illuminate\Queue\QueueManager $queue
     *
     * @return void
     */
    public function setupQueue(QueueManager $queue)
    {
        $callback = function () {
            $this->app->bugsnag->flush();
        };

        if (method_exists($queue, 'before')) {
            $queue->before($callback);
        }

        $queue->after($callback);
        $queue->stopping($callback);

        if (method_exists($queue, 'exceptionOccurred')) {
            $queue->exceptionOccurred($callback);
        } else {
            $queue->looping($callback);
        }

        if (method_exists($queue, 'before')) {
            $queue->before(function () {
                $this->app->bugsnag->clearBreadcrumbs();
            });
        } else {
            $queue->looping(function () {
                $this->app->bugsnag->clearBreadcrumbs();
            });
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

            $client = new Client(new Configuration($config['api_key']), new LaravelResolver($app), $this->getGuzzle($config));

            if (!isset($config['callbacks']) || $config['callbacks']) {
                $client->registerDefaultCallbacks();
            }

            if (!isset($config['user']) || $config['user']) {
                $client->registerCallback(new CustomUser(function () use ($app) {
                    if ($user = $app->auth->user()) {
                        return $user->toArray();
                    }
                }));
            }

            $client->setStripPath($app->basePath());
            $client->setProjectRoot($app->path());
            $client->setReleaseStage($app->environment());
            $client->setAppType($app->runningInConsole() ? 'Console' : 'Web');

            $client->setNotifier([
                'name' => 'Bugsnag Laravel',
                'version' => static::VERSION,
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
            return new LaravelLogger($app['bugsnag']);
        });

        $this->app->singleton('bugsnag.multi', function (Container $app) {
            return new MultiLogger([$app['log'], $app['bugsnag.logger']]);
        });

        $this->app->alias('bugsnag', Client::class);
        $this->app->alias('bugsnag.logger', LaravelLogger::class);
        $this->app->alias('bugsnag.multi', MultiLogger::class);
    }

    /**
     * Get the guzzle client instance.
     *
     * @param array $config
     *
     * @return \GuzzleHttp\ClientInterface
     */
    protected function getGuzzle(array $config)
    {
        $key = version_compare(ClientInterface::VERSION, '6') === 1 ? 'base_uri' : 'base_url';

        $options = [$key => isset($config['endpoint']) ? $config['endpoint'] : Client::ENDPOINT];

        if (isset($config['proxy']) && $config['proxy']) {
            if (isset($config['proxy']['http']) && php_sapi_name() != 'cli') {
                unset($config['proxy']['http']);
            }

            $options['proxy'] = $config['proxy'];
        }

        return new Guzzle($options);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['bugsnag', 'bugsnag.logger', 'bugsnag.multi'];
    }
}
