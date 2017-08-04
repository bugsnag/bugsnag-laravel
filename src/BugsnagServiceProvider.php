<?php

namespace Bugsnag\BugsnagLaravel;

use Bugsnag\Breadcrumbs\Breadcrumb;
use Bugsnag\BugsnagLaravel\Queue\Tracker;
use Bugsnag\BugsnagLaravel\Request\LaravelResolver;
use Bugsnag\Callbacks\CustomUser;
use Bugsnag\Client;
use Bugsnag\Configuration;
use Bugsnag\Report;
use Illuminate\Auth\GenericUser;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\QueueManager;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application as LumenApplication;
use ReflectionClass;

class BugsnagServiceProvider extends ServiceProvider
{
    /**
     * The package version.
     *
     * @var string
     */
    const VERSION = '2.6.0';

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
        $source = realpath($raw = __DIR__.'/../config/bugsnag.php') ?: $raw;

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
        if (isset($config['query']) && !$config['query']) {
            return;
        }

        $show = isset($config['bindings']) && $config['bindings'];

        if (class_exists(QueryExecuted::class)) {
            $events->listen(QueryExecuted::class, function (QueryExecuted $query) use ($show) {
                $this->app->bugsnag->leaveBreadcrumb(
                    'Query executed',
                    Breadcrumb::PROCESS_TYPE,
                    $this->formatQuery($query->sql, $show ? $query->bindings : [], $query->time, $query->connectionName)
                );
            });
        } else {
            $events->listen('illuminate.query', function ($sql, array $bindings, $time, $connection) use ($show) {
                $this->app->bugsnag->leaveBreadcrumb(
                    'Query executed',
                    Breadcrumb::PROCESS_TYPE,
                    $this->formatQuery($sql, $show ? $bindings : [], $time, $connection)
                );
            });
        }
    }

    /**
     * Format the query as breadcrumb metadata.
     *
     * @param string $sql
     * @param array  $bindings
     * @param float  $time
     * @param string $connection
     *
     * @return array
     */
    protected function formatQuery($sql, array $bindings, $time, $connection)
    {
        $data = ['sql' => $sql];

        foreach ($bindings as $index => $binding) {
            $data["binding {$index}"] = $binding;
        }

        $data['time'] = "{$time}ms";
        $data['connection'] = $connection;

        return $data;
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
            $this->app->bugsnag->setFallbackType('Queue');

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

            $this->setupCallbacks($client, $app, $config);
            $this->setupPaths($client, $app->basePath(), $app->path(), isset($config['strip_path']) ? $config['strip_path'] : null, isset($config['project_root']) ? $config['project_root'] : null);

            $client->setReleaseStage(isset($config['release_stage']) ? $config['release_stage'] : $app->environment());
            $client->setHostname(isset($config['hostname']) ? $config['hostname'] : null);

            $client->setFallbackType($app->runningInConsole() ? 'Console' : 'HTTP');
            $client->setAppType(isset($config['app_type']) ? $config['app_type'] : null);
            $client->setAppVersion(isset($config['app_version']) ? $config['app_version'] : null);
            $client->setBatchSending(isset($config['batch_sending']) ? $config['batch_sending'] : true);
            $client->setSendCode(isset($config['send_code']) ? $config['send_code'] : true);

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
            return new LaravelLogger($app['bugsnag'], $app['events']);
        });

        $this->app->singleton('bugsnag.multi', function (Container $app) {
            return new MultiLogger([$app['log'], $app['bugsnag.logger']], $app['events']);
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
     * Setup the callbacks.
     *
     * @param \Bugsnag\Client                           $client
     * @param \Illuminate\Contracts\Container\Container $app
     * @param array                                     $config
     *
     * @return void
     */
    protected function setupCallbacks(Client $client, Container $app, array $config)
    {
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
                    if (is_callable([$user, 'toArray'])) {
                        return $user->toArray();
                    }

                    if ($user instanceof GenericUser) {
                        $reflection = new ReflectionClass($user);
                        $property = $reflection->getProperty('attributes');
                        $property->setAccessible(true);

                        return $property->getValue($user);
                    }
                }
            }));
        }
    }

    /**
     * Setup the client paths.
     *
     * @param \Bugsnag\Client $client
     * @param string          $base
     * @param string          $path
     * @param string|null     $strip
     * @param string|null     $project
     *
     * @return void
     */
    protected function setupPaths(Client $client, $base, $path, $strip, $project)
    {
        if ($strip) {
            $client->setStripPath($strip);

            if (!$project) {
                $client->setProjectRoot("{$strip}/app");
            }

            return;
        }

        if ($project) {
            if ($base && substr($project, 0, strlen($base)) === $base) {
                $client->setStripPath($base);
            }

            $client->setProjectRoot($project);

            return;
        }

        $client->setStripPath($base);
        $client->setProjectRoot($path);
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
