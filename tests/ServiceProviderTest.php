<?php

namespace Bugsnag\BugsnagLaravel\Tests;

use Bugsnag\BugsnagLaravel\LaravelLogger;
use Bugsnag\BugsnagLaravel\MultiLogger;
use Bugsnag\BugsnagLaravel\Queue\Tracker;
use Bugsnag\Client;
use Bugsnag\Configuration;
use Bugsnag\PsrLogger\BugsnagLogger;
use Bugsnag\PsrLogger\MultiLogger as BaseMultiLogger;
use GrahamCampbell\TestBenchCore\ServiceProviderTrait;
use Illuminate\Contracts\Logging\Log;
use Illuminate\Foundation\Application;
use Mockery;

class ServiceProviderTest extends AbstractTestCase
{
    use ServiceProviderTrait;

    /**
     * An Application instance provided by the parent test case.
     *
     * @var Application
     */
    protected $app;

    public function testClientIsInjectable()
    {
        $this->assertIsInjectable(Client::class);
    }

    public function testJobTrackerIsInjectable()
    {
        $this->assertIsInjectable(Tracker::class);
    }

    public function testMultiLoggerIsInjectable()
    {
        $this->assertIsInjectable(interface_exists(Log::class) ? MultiLogger::class : BaseMultiLogger::class);
    }

    public function testBugsnagLoggerIsInjectable()
    {
        $this->assertIsInjectable(interface_exists(Log::class) ? LaravelLogger::class : BugsnagLogger::class);
    }

    /**
     * Ensure the project root and strip path are both set with sensible defaults
     * when no explicit configuration is provided.
     *
     * @return void
     */
    public function testProjectRootAndStripPathAreInferredWhenNoSpecificConfigurationIsGiven()
    {
        $client = $this->app->make(Client::class);

        $this->assertInstanceOf(Client::class, $client);

        $basePath = $this->app->basePath();
        $appRoot = $this->app->path();

        /** @var Client $client */
        $config = $client->getConfig();

        $projectRootRegex = $this->getProperty($config, 'projectRootRegex');
        $stripPathRegex = $this->getProperty($config, 'stripPathRegex');

        $expectedProjectRootRegex = sprintf('/^%s[\\/]?/i', preg_quote($appRoot, '/'));
        $expectedStripPathRegex = $expectedProjectRootRegex;

        $this->assertSame(
            $expectedStripPathRegex,
            $stripPathRegex,
            "Expected to set a sensible default for the 'stripPathRegex'"
        );
        $this->assertSame(
            $expectedProjectRootRegex,
            $projectRootRegex,
            "Expected to set a sensible default for the 'projectRootRegex'"
        );
    }

    /**
     * @param string|null $projectRoot
     * @param string|null $stripPath
     * @param string|null $expectedProjectRootRegex
     * @param string      $expectedStripPathRegex
     *
     * @return void
     *
     * @dataProvider projectRootAndStripPathProvider
     */
    public function testProjectRootAndStripPathAreSetCorrectly($projectRoot, $stripPath, $expectedProjectRootRegex, $expectedStripPathRegex)
    {
        /** @var \Illuminate\Config\Repository $laravelConfig */
        $laravelConfig = $this->app->config;
        $bugsnagConfig = $laravelConfig->get('bugsnag');

        $this->assertNull(
            $bugsnagConfig['project_root'],
            "Expected the default configuration value for 'project_root' to be null"
        );

        $this->assertNull(
            $bugsnagConfig['strip_path'],
            "Expected the default configuration value for 'strip_path' to be null"
        );

        $bugsnagConfig['project_root'] = $projectRoot;
        $bugsnagConfig['strip_path'] = $stripPath;

        $laravelConfig->set('bugsnag', $bugsnagConfig);

        $client = $this->app->make(Client::class);

        $this->assertInstanceOf(Client::class, $client);

        $basePath = $this->app->basePath();
        $appRoot = $this->app->path();

        /** @var Client $client */
        $config = $client->getConfig();

        $projectRootRegex = $this->getProperty($config, 'projectRootRegex');
        $stripPathRegex = $this->getProperty($config, 'stripPathRegex');

        $this->assertSame(
            $expectedStripPathRegex,
            $stripPathRegex,
            "Expected the 'stripPathRegex' to match the string provided in Bugsnag configuration"
        );

        $this->assertSame(
            $expectedProjectRootRegex,
            $projectRootRegex,
            "Expected the 'projectRootRegex' to match the string provided in Bugsnag configuration"
        );
    }

    public function projectRootAndStripPathProvider()
    {
        return [
            // If both strings are provided, the project root should be null
            // and the strip path set to a regex matching the given string
            // TODO this might be a bug — when a strip path value is configured,
            //      we only set the project root path if one wasn't provided. If both
            //      strip path _and_ project root are given, no project root is set
            'both strings provided' => [
                'project_root' => '/example/project/root',
                'strip_path' => '/example/strip/path',
                'expected_project_root_regex' => null,
                'expected_strip_path_regex' => $this->pathToRegex('/example/strip/path'),
            ],

            // If only the project root string is provided, both values should be
            // set to the same regex matching the given project root string
            'only project root string provided' => [
                'project_root' => '/example/project/root',
                'strip_path' => null,
                'expected_project_root_regex' => $this->pathToRegex('/example/project/root'),
                'expected_strip_path_regex' => $this->pathToRegex('/example/project/root'),
            ],

            // If the project root string matches the app's base path, we set
            // the strip path to match too. The base path in the tests is set by
            // the TestBench package
            'only project root string provided (to root of test app)' => [
                'project_root' => realpath(__DIR__.'/../vendor/orchestra/testbench-core/laravel'),
                'strip_path' => null,
                'expected_project_root_regex' => $this->pathToRegex(realpath(__DIR__.'/../vendor/orchestra/testbench-core/laravel')),
                'expected_strip_path_regex' => $this->pathToRegex(realpath(__DIR__.'/../vendor/orchestra/testbench-core/laravel')),
            ],

            // If only the strip path string is provided, both values should be
            // set to the same regex matching the given strip path string with "/app"
            // appended
            // TODO this might be a bug — when only strip path is configured,
            //      we set the strip path and then immediately overwrite it with
            //      a new path with "/app" appended by calling `setProjectRoot`.
            //      If the intention is to to have "/example" be the strip path
            //      and "/example/app" be the project root then we need to do these
            //      calls in the opposite order
            'only strip path string provided' => [
                'project_root' => null,
                'strip_path' => '/example/strip/path',
                'expected_project_root_regex' => $this->pathToRegex('/example/strip/path/app'),
                'expected_strip_path_regex' => $this->pathToRegex('/example/strip/path/app'),
            ],
        ];
    }

    /**
     * Convert a file path to a regex that matches the path and any sub paths.
     *
     * @param string $path
     *
     * @return string
     */
    private function pathToRegex($path)
    {
        return sprintf('/^%s[\\/]?/i', preg_quote($path, '/'));
    }

    public function testCorrectLoggerClassesReturned()
    {
        $app = Mockery::mock(Application::class);
        $providerClass = $this->getServiceProviderClass($app);
        $provider = new $providerClass($app);

        $loggerClass = interface_exists(Log::class) ? LaravelLogger::class : BugsnagLogger::class;
        $multiLoggerClass = interface_exists(Log::class) ? MultiLogger::class : BaseMultiLogger::class;

        $app->shouldReceive('singleton')
            ->with('bugsnag', \Mockery::type('callable'))
            ->once();

        $app->shouldReceive('singleton')
            ->with('bugsnag.tracker', \Mockery::type('callable'))
            ->once();

        $app->shouldReceive('singleton')
            ->with('bugsnag.logger', \Mockery::on(
                function ($closure) use ($loggerClass) {
                    if (is_callable($closure)) {
                        $internalApp = Mockery::mock(Application::class);
                        $internalApp->shouldReceive('offsetGet')->with('config')->andReturn($internalApp);
                        $internalApp->shouldReceive('get')->with('bugsnag')->andReturn([
                            'logger_notify_level' => 'error',
                        ]);
                        $bugsnag = Mockery::mock(Client::class);
                        $internalApp->shouldReceive('offsetGet')->with('bugsnag')->andReturn($bugsnag);
                        $internalApp->shouldReceive('offsetGet')->with('events')->zeroOrMoreTimes();
                        $logger = call_user_func($closure, $internalApp);
                        $this->assertSame(get_class($logger), $loggerClass);

                        return true;
                    }

                    return false;
                }
            ))
            ->once();

        $app->shouldReceive('singleton')
            ->with('bugsnag.multi', \Mockery::on(
                function ($closure) use ($multiLoggerClass) {
                    if (is_callable($closure)) {
                        $internalApp = Mockery::mock(Application::class);
                        $internalApp->shouldReceive('offsetGet')->with(\Mockery::type('string'));
                        $multiLogger = call_user_func($closure, $internalApp);
                        $this->assertSame(get_class($multiLogger), $multiLoggerClass);

                        return true;
                    }

                    return false;
                }
            ))
            ->once();

        $app->shouldReceive('offsetGet')->with('log')->andReturn(null);
        $app->shouldReceive('alias')->with('bugsnag', Client::class)->once();
        $app->shouldReceive('alias')->with('bugsnag.tracker', Tracker::class)->once();
        $app->shouldReceive('alias')->with('bugsnag.logger', $loggerClass)->once();
        $app->shouldReceive('alias')->with('bugsnag.multi', $multiLoggerClass)->once();
        $provider->register();
    }
}
