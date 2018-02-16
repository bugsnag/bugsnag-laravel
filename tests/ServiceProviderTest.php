<?php

namespace Bugsnag\BugsnagLaravel\Tests;

use Bugsnag\BugsnagLaravel\LaravelLogger;
use Bugsnag\BugsnagLaravel\MultiLogger;
use Bugsnag\BugsnagLaravel\Queue\Tracker;
use Bugsnag\Client;
use Bugsnag\PsrLogger\BugsnagLogger;
use Bugsnag\PsrLogger\MultiLogger as BaseMultiLogger;
use GrahamCampbell\TestBenchCore\ServiceProviderTrait;
use Illuminate\Contracts\Logging\Log;

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
        $this->assertIsInjectable(class_exists(Log::class) ? MultiLogger::class : BaseMultiLogger::class);
    }

    public function testBugsnagLoggerIsInjectable()
    {
        $this->assertIsInjectable(class_exists(Log::class) ? LaravelLogger::class : BugsnagLogger::class);
    }
}
