<?php

namespace Bugsnag\BugsnagLaravel\Tests;

use Bugsnag\BugsnagLaravel\BugsnagServiceProvider;
use Bugsnag\BugsnagLaravel\BugsnagLogger;
use Bugsnag\Client;
use GrahamCampbell\TestBenchCore\ServiceProviderTrait;

class ServiceProviderTest extends AbstractTestCase
{
    use ServiceProviderTrait;

    public function testClientIsInjectable()
    {
        $this->app->config->set('bugsnag.api_key', 'qwertyuiop');

        $this->assertIsInjectable(Client::class);
    }

    public function testLoggerIsInjectable()
    {
        $this->app->config->set('bugsnag.api_key', 'qwertyuiop');

        $this->assertIsInjectable(BugsnagLogger::class);
    }
}
