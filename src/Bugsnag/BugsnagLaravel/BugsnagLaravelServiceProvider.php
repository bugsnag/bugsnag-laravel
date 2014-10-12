<?php namespace Bugsnag\BugsnagLaravel;

use Illuminate\Support\ServiceProvider;

class BugsnagLaravelServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('bugsnag/bugsnag-laravel', 'bugsnag');

        $app = $this->app;

        // Register for exception handling
        $app->error(function (\Exception $exception) use ($app) {
            $app['bugsnag']->notifyException($exception);
        });

        // Register for fatal error handling
        $app->fatal(function ($exception) use ($app) {
            $app['bugsnag']->notifyException($exception);
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('bugsnag', function ($app) {
            $config = $app['config']['bugsnag'] ?: $app['config']['bugsnag::config'];

            $client = new \Bugsnag_Client($config['api_key']);
            $client->setStripPath(base_path());
            $client->setProjectRoot(app_path());
            $client->setAutoNotify(false);
            $client->setBatchSending(false);
            $client->setReleaseStage($app->environment());
            $client->setNotifier(array(
                'name'    => 'Bugsnag Laravel',
                'version' => '1.1.0',
                'url'     => 'https://github.com/bugsnag/bugsnag-laravel'
            ));

            if (is_array($stages = $config['notify_release_stages'])) {
                $client->setNotifyReleaseStages($stages);
            }

            if (isset($config['endpoint'])) {
                $client->setEndpoint($config['endpoint']);
            }

            if (isset($config['use_ssl'])) {
                $client->setUseSSL($config['use_ssl']);
            }

            if (is_array($filters = $config['filters'])) {
                $client->setFilters($filters);
            }

            if (is_array($proxy = $config['proxy'])) {
                $client->setProxySettings($proxy);
            }

            // Check if someone is logged in.
            if ($app['auth']->check()) {
                // User is logged in.
                $user = $app['auth']->user()->toArray();

                // If these attributes are available: pass them on.
                $client->setUser(array_only($user, array('id', 'email')));
            }

            return $client;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array("bugsnag");
    }
}
