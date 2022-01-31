<?php

// stub out php_sapi_name in the Illumnate\Foundation namespace so we can change
// its behaviour. Laravel 5.0 - 5.8 use this to determine if the app is running
// in a console (6.0+ use an environment variable or the PHP_SAPI constant)

namespace Illuminate\Foundation;

use Bugsnag\BugsnagLaravel\Tests\Stubs\PhpSapiNameStub;

function php_sapi_name()
{
    return PhpSapiNameStub::get();
}

namespace Bugsnag\BugsnagLaravel\Tests\Request;

use Bugsnag\BugsnagLaravel\Request\LaravelRequest;
use Bugsnag\BugsnagLaravel\Request\LaravelResolver;
use Bugsnag\BugsnagLaravel\Tests\AbstractTestCase;
use Bugsnag\BugsnagLaravel\Tests\Stubs\PhpSapiNameStub;
use Bugsnag\Request\ConsoleRequest;
use Bugsnag\Request\RequestInterface;

class LaravelRequestTest extends AbstractTestCase
{
    public function testCanResolveConsoleRequest()
    {
        $resolver = new LaravelResolver($this->app);
        $result = $resolver->resolve();

        $this->assertInstanceOf(RequestInterface::class, $result);
        $this->assertInstanceOf(ConsoleRequest::class, $result);
    }

    public function testCanResolveLaravelRequest()
    {
        try {
            // different versions of Laravel use slightly different mechanisms
            // to determine if the app is 'runningInConsole'
            PhpSapiNameStub::set('not cli');
            $this->setEnvironmentVariable('APP_RUNNING_IN_CONSOLE', false);

            // At this point we already have a Laravel app loaded so the environment
            // variable won't be picked up. Refreshing the application forces
            // the config to re-load
            $this->refreshApplication();

            $resolver = new LaravelResolver($this->app);
            $result = $resolver->resolve();

            $this->assertInstanceOf(RequestInterface::class, $result);
            $this->assertInstanceOf(LaravelRequest::class, $result);
        } finally {
            PhpSapiNameStub::reset();
            $this->removeEnvironmentVariable('APP_RUNNING_IN_CONSOLE');
        }
    }
}
