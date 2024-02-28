<?php

namespace Bugsnag\BugsnagLaravel\Tests;

use Bugsnag\BugsnagLaravel\BugsnagServiceProvider;
use Orchestra\Testbench\TestCase;

abstract class AbstractTestCase extends TestCase
{
    /**
     * Get the service provider class.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     *
     * @return array<string>
     */
    protected function getPackageProviders($app)
    {
        return [
            BugsnagServiceProvider::class,
        ];
    }

    /**
     * Get the value of the given property on the given object.
     *
     * @param object $object
     * @param string $property
     *
     * @return mixed
     */
    protected function getProperty($object, $property)
    {
        $propertyAccessor = function ($property) {
            return $this->{$property};
        };

        return call_user_func($propertyAccessor->bindTo($object, $object), $property);
    }

    /**
     * Set the environment variable "$name" to the given value.
     *
     * @param string $name
     * @param string $value
     *
     * @return void
     */
    protected function setEnvironmentVariable($name, $value)
    {
        // Workaround a PHP 5 parser issue - '$app::VERSION' is valid but
        // '$this->app::VERSION' is not
        $app = $this->app;

        // Laravel >= 5.8.0 uses "$_ENV" instead of "putenv" by default
        if (version_compare($app::VERSION, '5.8.0', '>=')) {
            $_ENV[$name] = $value;
        } else {
            putenv("{$name}={$value}");
        }
    }

    /**
     * Remove the environment variable "$name" from the environment.
     *
     * @param string $name
     * @param string $value
     *
     * @return void
     */
    protected function removeEnvironmentVariable($name)
    {
        // Workaround a PHP 5 parser issue - '$app::VERSION' is valid but
        // '$this->app::VERSION' is not
        $app = $this->app;

        // Laravel >= 5.8.0 uses "$_ENV" instead of "putenv" by default
        if (version_compare($app::VERSION, '5.8.0', '>=')) {
            unset($_ENV[$name]);
        } else {
            putenv("{$name}");
        }
    }

    /**
     * A getter for the laravel app's base path that's backwards compatible with
     * testbench v3
     *
     * @return string
     */
    protected static function backwardsCompatibleGetApplicationBasePath()
    {
        // testbench v4+
        if (method_exists(static::class, 'applicationBasePath')) {
            return static::applicationBasePath();
        }

        static $applicationBasePath;

        if ($applicationBasePath) {
            return $applicationBasePath;
        }

        $packages = ['testbench', 'testbench-core'];
        $fixtureDirectories = ['fixture', 'laravel'];
        $sourceDirectories = ['Concerns', 'Traits'];
        $vendorDirectory = realpath(__DIR__.'/../vendor');

        foreach ($packages as $package) {
            foreach ($fixtureDirectories as $fixtureDirectory) {
                foreach ($sourceDirectories as $sourceDirectory) {
                    $path = "{$vendorDirectory}/orchestra/{$package}/src/{$sourceDirectory}/../../{$fixtureDirectory}";

                    if (is_readable($path)) {
                        return $applicationBasePath = $path;
                    }
                }
            }
        }

        throw new Exception('Unable to determine application base path!');
    }
}
