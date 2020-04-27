<?php

namespace Bugsnag\BugsnagLaravel\Tests;

use Bugsnag\BugsnagLaravel\BugsnagServiceProvider;
use GrahamCampbell\TestBench\AbstractPackageTestCase;

abstract class AbstractTestCase extends AbstractPackageTestCase
{
    /**
     * Get the service provider class.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     *
     * @return string
     */
    protected function getServiceProviderClass($app)
    {
        return BugsnagServiceProvider::class;
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

        return $propertyAccessor->call($object, $property);
    }
}
