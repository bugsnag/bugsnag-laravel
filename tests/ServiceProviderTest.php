<?php

namespace Bugsnag\BugsnagLaravel\Tests;

use Bugsnag\BugsnagLaravel\BugsnagServiceProvider;
use Bugsnag\BugsnagLaravel\LaravelLogger;
use Bugsnag\BugsnagLaravel\MultiLogger;
use Bugsnag\BugsnagLaravel\Queue\Tracker;
use Bugsnag\BugsnagLaravel\Tests\Stubs\Injectee;
use Bugsnag\BugsnagLaravel\Tests\Stubs\InjecteeWithLogInterface;
use Bugsnag\Client;
use Bugsnag\FeatureFlag;
use Bugsnag\PsrLogger\BugsnagLogger;
use Bugsnag\PsrLogger\MultiLogger as BaseMultiLogger;
use Illuminate\Contracts\Logging\Log;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class ServiceProviderTest extends AbstractTestCase
{
    /**
     * An Application instance provided by the parent test case.
     *
     * @var Application
     */
    protected $app;

    public function testItIsAServiceProvider()
    {
        $serviceProvider = $this->app->getProvider(BugsnagServiceProvider::class);

        $this->assertInstanceOf(ServiceProvider::class, $serviceProvider);
    }

    public function testItProvidesAValidListOfServices()
    {
        $serviceProvider = $this->app->getProvider(BugsnagServiceProvider::class);
        $provides = $serviceProvider->provides();

        // ensure there's at least one provided service
        $this->assertNotEmpty($provides);

        foreach ($provides as $serviceId) {
            // assert this service exists in the container
            $this->assertTrue(
                $this->app->bound($serviceId),
                "Expected the ID '{$serviceId}' to be bound in the DI container"
            );

            // ensure the service can be resolved
            $this->app->make($serviceId);
        }
    }

    /**
     * @dataProvider serviceAliasProvider
     */
    public function testItRegistersAnAliasForEachService($serviceId, $alias)
    {
        $this->assertTrue(
            $this->app->bound($serviceId),
            "Expected the ID '{$serviceId}' to be bound in the DI container"
        );

        $this->assertTrue(
            $this->app->bound($alias),
            "Expected the ID '{$alias}' to be bound in the DI container"
        );

        $service = $this->app->make($serviceId);
        $aliasInstance = $this->app->make($alias);

        $this->assertSame($service, $aliasInstance);
    }

    public static function serviceAliasProvider()
    {
        return [
            'bugsnag' => ['bugsnag', Client::class],
            'bugsnag.tracker' => ['bugsnag.tracker', Tracker::class],
            'bugsnag.logger' => ['bugsnag.logger', interface_exists(Log::class) ? LaravelLogger::class : BugsnagLogger::class],
            'bugsnag.multi' => ['bugsnag.multi', interface_exists(Log::class) ? MultiLogger::class : BaseMultiLogger::class],
        ];
    }

    public function testServicesAreInjectable()
    {
        if (interface_exists(Log::class)) {
            $injectee = InjecteeWithLogInterface::class;
        } else {
            $injectee = Injectee::class;
        }

        $service = $this->app->make($injectee);

        $this->assertTrue($service->wasConstructed());
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

        /** @var Client $client */
        $config = $client->getConfig();

        $projectRootRegex = $this->getProperty($config, 'projectRootRegex');
        $stripPathRegex = $this->getProperty($config, 'stripPathRegex');

        $expectedProjectRootRegex = self::pathToRegex($this->app->path());
        $expectedStripPathRegex = self::pathToRegex($this->app->basePath());

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
     * @param string|null $projectRootRegex
     * @param string|null $stripPathRegex
     * @param string|null $expectedProjectRootRegex
     * @param string      $expectedStripPathRegex
     *
     * @return void
     *
     * @dataProvider projectRootAndStripPathProvider
     */
    public function testProjectRootAndStripPathAreSetCorrectly(
        $projectRoot,
        $stripPath,
        $projectRootRegex,
        $stripPathRegex,
        $expectedProjectRootRegex,
        $expectedStripPathRegex
    ) {
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

        $this->assertNull(
            $bugsnagConfig['project_root_regex'],
            "Expected the default configuration value for 'project_root_regex' to be null"
        );

        $this->assertNull(
            $bugsnagConfig['strip_path_regex'],
            "Expected the default configuration value for 'strip_path_regex' to be null"
        );

        $bugsnagConfig['project_root'] = $projectRoot;
        $bugsnagConfig['strip_path'] = $stripPath;
        $bugsnagConfig['project_root_regex'] = $projectRootRegex;
        $bugsnagConfig['strip_path_regex'] = $stripPathRegex;

        $laravelConfig->set('bugsnag', $bugsnagConfig);

        $client = $this->app->make(Client::class);

        $this->assertInstanceOf(Client::class, $client);

        /** @var Client $client */
        $config = $client->getConfig();

        $projectRootRegex = $this->getProperty($config, 'projectRootRegex');
        $stripPathRegex = $this->getProperty($config, 'stripPathRegex');

        $this->assertSame(
            $expectedProjectRootRegex,
            $projectRootRegex,
            "Expected the 'projectRootRegex' to match the string provided in Bugsnag configuration"
        );

        $this->assertSame(
            $expectedStripPathRegex,
            $stripPathRegex,
            "Expected the 'stripPathRegex' to match the string provided in Bugsnag configuration"
        );
    }

    public static function projectRootAndStripPathProvider()
    {
        return [
            // If both strings are provided, both options should be set to the
            // regex version of the given strings
            'both strings provided' => [
                'project_root' => '/example/project/root',
                'strip_path' => '/example/strip/path',
                'project_root_regex' => null,
                'strip_path_regex' => null,
                'expected_project_root_regex' => self::pathToRegex('/example/project/root'),
                'expected_strip_path_regex' => self::pathToRegex('/example/strip/path'),
            ],

            // If both regexes are provided they should be set verbatim
            'both regexes provided' => [
                'project_root' => null,
                'strip_path' => null,
                'project_root_regex' => '/^example project root regex/',
                'strip_path_regex' => '/^example strip path regex/',
                'expected_project_root_regex' => '/^example project root regex/',
                'expected_strip_path_regex' => '/^example strip path regex/',
            ],

            // If only the project root string is provided, the project root should
            // be set to the regex version of the string and the strip path to
            // the application base path
            'only project root string provided' => [
                'project_root' => '/example/project/root',
                'strip_path' => null,
                'project_root_regex' => null,
                'strip_path_regex' => null,
                'expected_project_root_regex' => self::pathToRegex('/example/project/root'),
                'expected_strip_path_regex' => self::pathToRegex(self::backwardsCompatibleGetApplicationBasePath()),
            ],

            // If only the project root regex is provided, both values should be
            // set to the same regex
            'only project root regex provided' => [
                'project_root' => null,
                'strip_path' => null,
                'project_root_regex' => '/^example project root regex/',
                'strip_path_regex' => null,
                'expected_project_root_regex' => '/^example project root regex/',
                'expected_strip_path_regex' => self::pathToRegex(self::backwardsCompatibleGetApplicationBasePath()),
            ],

            // If only the strip path string is provided, both values should be
            // set — the stip path to the regex version of the string and the
            // project root with "/app" appended
            'only strip path string provided' => [
                'project_root' => null,
                'strip_path' => '/example/strip/path',
                'project_root_regex' => null,
                'strip_path_regex' => null,
                'expected_project_root_regex' => self::pathToRegex(self::backwardsCompatibleGetApplicationBasePath()."/app"),
                'expected_strip_path_regex' => self::pathToRegex('/example/strip/path'),
            ],

            // If only the strip path regex is provided, the strip path should be
            // set verbatim and the project root should be set to the application
            // path (i.e. "/base/path/app")
            'only strip path regex provided' => [
                'project_root' => null,
                'strip_path' => null,
                'project_root_regex' => null,
                'strip_path_regex' => '/^example strip path regex/',
                'expected_project_root_regex' => self::pathToRegex(self::backwardsCompatibleGetApplicationBasePath()."/app"),
                'expected_strip_path_regex' => '/^example strip path regex/',
            ],

            // If the regexes are provided and either string value is too then
            // the regexes should take precedence and the string value ignored
            'project root string and both regexes provided' => [
                'project_root' => self::pathToRegex('/example/project/root'),
                'strip_path' => null,
                'project_root_regex' => '/^example project root regex/',
                'strip_path_regex' => '/^example strip path regex/',
                'expected_project_root_regex' => '/^example project root regex/',
                'expected_strip_path_regex' => '/^example strip path regex/',
            ],

            // If the regexes are provided and either string value is too then
            // the regexes should take precedence and the string value ignored
            'strip path string and both regexes provided' => [
                'project_root' => null,
                'strip_path' => self::pathToRegex('/example/strip/path'),
                'project_root_regex' => '/^example project root regex/',
                'strip_path_regex' => '/^example strip path regex/',
                'expected_project_root_regex' => '/^example project root regex/',
                'expected_strip_path_regex' => '/^example strip path regex/',
            ],

            // If all four options are provided then the regexes should take
            // precedence and the string values ignored
            'all options provided' => [
                'project_root' => self::pathToRegex('/example/project/root'),
                'strip_path' => self::pathToRegex('/example/strip/path'),
                'project_root_regex' => '/^example project root regex/',
                'strip_path_regex' => '/^example strip path regex/',
                'expected_project_root_regex' => '/^example project root regex/',
                'expected_strip_path_regex' => '/^example strip path regex/',
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
    private static function pathToRegex($path)
    {
        return sprintf('/^%s[\\/]?/i', preg_quote($path, '/'));
    }

    public function testCorrectLoggerClassesReturned()
    {
        $loggerClass = interface_exists(Log::class) ? LaravelLogger::class : BugsnagLogger::class;
        $multiLoggerClass = interface_exists(Log::class) ? MultiLogger::class : BaseMultiLogger::class;

        $logger = $this->app->make('bugsnag.logger');
        $this->assertInstanceOf($loggerClass, $logger);

        $multiLogger = $this->app->make('bugsnag.multi');
        $this->assertInstanceOf($multiLoggerClass, $multiLogger);
    }

    public function testFiltersUseDefaultsIfNull()
    {
        /** @var \Illuminate\Config\Repository $laravelConfig */
        $laravelConfig = $this->app->config;
        $bugsnagConfig = $laravelConfig->get('bugsnag');

        $this->assertNull(
            $bugsnagConfig['filters'],
            "Expected the default configuration value for 'filters' to be null"
        );

        $client = $this->app->make(Client::class);
        $this->assertInstanceOf(Client::class, $client);

        /** @var Client $client */
        $config = $client->getConfig();

        $this->assertNotEmpty($config->getFilters());
    }

    public function testFiltersUseProvdedValueIfArray()
    {
        /** @var \Illuminate\Config\Repository $laravelConfig */
        $laravelConfig = $this->app->config;
        $bugsnagConfig = $laravelConfig->get('bugsnag');
        $bugsnagConfig['filters'] = ['abc', 'xyz'];

        $laravelConfig->set('bugsnag', $bugsnagConfig);

        $client = $this->app->make(Client::class);
        $this->assertInstanceOf(Client::class, $client);

        /** @var Client $client */
        $config = $client->getConfig();

        $this->assertSame(['abc', 'xyz'], $config->getFilters());
    }

    public function testFiltersUseDefaultsIfNotProvidedAnArray()
    {
        /** @var \Illuminate\Config\Repository $laravelConfig */
        $laravelConfig = $this->app->config;
        $bugsnagConfig = $laravelConfig->get('bugsnag');

        $bugsnagConfig['filters'] = 'filtering';

        $laravelConfig->set('bugsnag', $bugsnagConfig);

        $client = $this->app->make(Client::class);
        $this->assertInstanceOf(Client::class, $client);

        /** @var Client $client */
        $config = $client->getConfig();

        $this->assertNotEquals('filtering', $config->getFilters());
        $this->assertNotEmpty($config->getFilters());
    }

    public function testFiltersUseEnvironmentVariableIfProvided()
    {
        try {
            $this->setEnvironmentVariable('BUGSNAG_FILTERS', 'abc, xyz , hello,hi, h e y ');

            // At this point we already have a Laravel app loaded so the environment
            // variable won't be picked up. Refreshing the application forces
            // the config to re-load so that 'BUGSNAG_FILTERS' is used
            $this->refreshApplication();

            $client = $this->app->make(Client::class);
            $this->assertInstanceOf(Client::class, $client);

            /** @var Client $client */
            $config = $client->getConfig();

            $this->assertSame(
                ['abc', 'xyz', 'hello', 'hi', 'hey'],
                $config->getFilters()
            );
        } finally {
            $this->removeEnvironmentVariable('BUGSNAG_FILTERS');
        }
    }

    public function testItUsesGuzzleInstanceFromTheContainer()
    {
        $guzzleClient = new \GuzzleHttp\Client();

        $this->app->singleton('bugsnag.guzzle', function () use ($guzzleClient) {
            return $guzzleClient;
        });

        $client = $this->app->make(Client::class);

        $httpClient = $this->getProperty($client, 'http');
        $actual = $this->getProperty($httpClient, 'guzzle');

        $this->assertSame($guzzleClient, $actual);
    }

    /**
     * @param int|null $memoryLimitIncrease
     *
     * @return void
     *
     * @dataProvider memoryLimitIncreaseProvider
     */
    public function testMemoryLimitIncreaseIsSetCorrectly($memoryLimitIncrease)
    {
        /** @var \Illuminate\Config\Repository $laravelConfig */
        $laravelConfig = $this->app->config;
        $bugsnagConfig = $laravelConfig->get('bugsnag');

        $this->assertFalse(array_key_exists('memory_limit_increase', $bugsnagConfig));

        $bugsnagConfig['memory_limit_increase'] = $memoryLimitIncrease;

        $laravelConfig->set('bugsnag', $bugsnagConfig);

        /** @var Client $client */
        $client = $this->app->make(Client::class);

        $this->assertInstanceOf(Client::class, $client);
        $this->assertSame($memoryLimitIncrease, $client->getMemoryLimitIncrease());
    }

    public static function memoryLimitIncreaseProvider()
    {
        return [
            [null],
            [1234],
            [1024 * 1024 * 20],
        ];
    }

    public function testFeatureFlagsCanBeSetFromConfig()
    {
        /** @var \Illuminate\Config\Repository $laravelConfig */
        $laravelConfig = $this->app->config;
        $bugsnagConfig = $laravelConfig->get('bugsnag');
        $bugsnagConfig['feature_flags'] = [
            ['name' => 'flag1'],
            ['name' => 'flag2', 'variant' => 'yes'],
            ['not name' => 'flag3'],
            ['name' => 'flag4', 'not variant' => 'xyz'],
        ];

        $laravelConfig->set('bugsnag', $bugsnagConfig);

        /** @var Client $client */
        $client = $this->app->make(Client::class);

        $this->assertInstanceOf(Client::class, $client);

        $expected = [
            new FeatureFlag('flag1'),
            new FeatureFlag('flag2', 'yes'),
            new FeatureFlag('flag4'),
        ];

        $actual = $client->getConfig()->getFeatureFlagsCopy()->toArray();

        $this->assertEquals($expected, $actual);
    }

    public function testFeatureFlagsAreNotSetWhenNotAnArray()
    {
        /** @var \Illuminate\Config\Repository $laravelConfig */
        $laravelConfig = $this->app->config;
        $bugsnagConfig = $laravelConfig->get('bugsnag');
        $bugsnagConfig['feature_flags'] = new \stdClass();

        $laravelConfig->set('bugsnag', $bugsnagConfig);

        /** @var Client $client */
        $client = $this->app->make(Client::class);

        $this->assertInstanceOf(Client::class, $client);

        $actual = $client->getConfig()->getFeatureFlagsCopy()->toArray();

        $this->assertSame([], $actual);
    }

    public function testMaxBreadcrumbsCanBeSetFromConfig()
    {
        /** @var \Illuminate\Config\Repository $laravelConfig */
        $laravelConfig = $this->app->config;
        $bugsnagConfig = $laravelConfig->get('bugsnag');
        $bugsnagConfig['max_breadcrumbs'] = 73;

        $laravelConfig->set('bugsnag', $bugsnagConfig);

        /** @var Client $client */
        $client = $this->app->make(Client::class);

        $this->assertInstanceOf(Client::class, $client);
        $this->assertSame(73, $client->getMaxBreadcrumbs());
    }
}
