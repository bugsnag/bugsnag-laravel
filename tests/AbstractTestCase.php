<?php

namespace Bugsnag\BugsnagLaravel\Tests;

use Bugsnag\BugsnagLaravel\BugsnagServiceProvider;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Orchestra\Testbench\TestCase;

abstract class AbstractTestCase extends TestCase
{
    use MockeryPHPUnitIntegration;

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
}
