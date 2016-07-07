<?php

namespace Bugsnag\BugsnagLaravel\Tests;

use Bugsnag\BugsnagLaravel\LaravelLogger;
use Bugsnag\Client;
use Bugsnag\PsrLogger\MultiLogger;
use GrahamCampbell\TestBenchCore\ServiceProviderTrait;

class ServiceProviderTest extends AbstractTestCase
{
    use ServiceProviderTrait;

    public function testClientIsInjectable()
    {
        $this->assertIsInjectable(Client::class);
    }

    public function testBugsnagLoggerIsInjectable()
    {
        $this->assertIsInjectable(LaravelLogger::class);
    }

    public function testMultiLoggerIsInjectable()
    {
        $this->assertIsInjectable(MultiLogger::class);
    }
}
