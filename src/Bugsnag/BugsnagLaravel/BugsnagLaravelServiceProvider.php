<?php namespace Bugsnag\BugsnagLaravel;

use Illuminate\Support\ServiceProvider;

class BugsnagLaravelServiceProvider extends ServiceProvider {

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
		$app->error(function(\Exception $exception) use ($app) {
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
		$this->app->singleton('bugsnag', function($app) {
			$config = $app['config']['bugsnag'] ?: $app['config']['bugsnag::config'];

			$client = new \Bugsnag_Client($config['api_key']);

			// TODO: Set releaseStage, etc from config if present

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