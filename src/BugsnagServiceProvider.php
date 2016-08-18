<?php

namespace Bugsnag\BugsnagLaravel;

use Bugsnag\Breadcrumbs\Breadcrumb;
use Bugsnag\BugsnagLaravel\Queue\Tracker;
use Bugsnag\BugsnagLaravel\Request\LaravelResolver;
use Bugsnag\Callbacks\CustomUser;
use Bugsnag\Client;
use Bugsnag\Configuration;
use Bugsnag\Report;
use Exception;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Queue\Events\JobProcessing;
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
    const VERSION = '2.2.0';

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->setupConfig($this->app);

        $this->setupEvents($this->app->events, $this->app->config->get('bugsnag'));

        $this->setupQueue($this->app->queue);
    }

    /**
     * Setup the config.
     *
     * @param \Illuminate\Contracts\Container\Container $app
     *
     * @return void
     */
    protected function setupConfig(Container $app)
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
     * @param array                                   $config
     *
     * @return void
     */
    protected function setupEvents(Dispatcher $events, array $config)
    {
        if (isset($config['events']) && !$config['events']) {
            return;
        }

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
    protected function setupQueue(QueueManager $queue)
    {
        $queue->looping(function () {
            $this->app->bugsnag->flush();
            $this->app->bugsnag->clearBreadcrumbs();
            $this->app->make(Tracker::class)->clear();
        });

        if (!class_exists(JobProcessing::class)) {
            return;
        }

        $queue->before(function (JobProcessing $event) {
            $this->app->bugsnag->setAppType('Queue');

            $job = [
                'name' => $event->job->getName(),
                'queue' => $event->job->getQueue(),
                'attempts' => $event->job->attempts(),
                'connection' => $event->connectionName,
            ];

            if (method_exists($event->job, 'resolveName')) {
                $job['resolved'] = $event->job->resolveName();
            }

            $this->app->make(Tracker::class)->set($job);
        });
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

                $client->registerCallback(function (Report $report) use ($app) {
                    $tracker = $app->make(Tracker::class);

                    if ($context = $tracker->context()) {
                        $report->setContext($context);
                    }

                    if ($job = $tracker->get()) {
                        $report->setMetaData(['job' => $job]);
                    }
                });
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
            $client->setAppType($app->runningInConsole() ? 'Console' : 'HTTP');

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

        $this->app->singleton('bugsnag.tracker', function () {
            return new Tracker();
        });

        $this->app->singleton('bugsnag.logger', function (Container $app) {
            return new LaravelLogger($app['bugsnag']);
        });

        $this->app->singleton('bugsnag.multi', function (Container $app) {
            return new MultiLogger([$app['log'], $app['bugsnag.logger']]);
        });

        $this->app->alias('bugsnag', Client::class);
        $this->app->alias('bugsnag.tracker', Tracker::class);
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
        $options = [];

        if (isset($config['proxy']) && $config['proxy']) {
            if (isset($config['proxy']['http']) && php_sapi_name() != 'cli') {
                unset($config['proxy']['http']);
            }

            $options['proxy'] = $config['proxy'];
        }

        return Client::makeGuzzle(isset($config['endpoint']) ? $config['endpoint'] : null, $options);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['bugsnag', 'bugsnag.tracker', 'bugsnag.logger', 'bugsnag.multi'];
    }
}
