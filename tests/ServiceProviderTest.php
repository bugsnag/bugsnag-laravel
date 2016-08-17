<?php

namespace Bugsnag\BugsnagLaravel\Tests;

use Bugsnag\BugsnagLaravel\LaravelLogger;
use Bugsnag\BugsnagLaravel\MultiLogger;
use Bugsnag\BugsnagLaravel\Queue\Tracker;
use Bugsnag\Client;
use GrahamCampbell\TestBenchCore\ServiceProviderTrait;

class ServiceProviderTest extends AbstractTestCase
{
    use ServiceProviderTrait;

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
        $this->assertIsInjectable(MultiLogger::class);
    }

    public function testBugsnagLoggerIsInjectable()
    {
        $this->assertIsInjectable(LaravelLogger::class);
    }
}
