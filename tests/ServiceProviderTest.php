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
use Illuminate\Foundation\Application;
use Mockery;

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
        $this->assertIsInjectable(interface_exists(Log::class) ? MultiLogger::class : BaseMultiLogger::class);
    }

    public function testBugsnagLoggerIsInjectable()
    {
        $this->assertIsInjectable(interface_exists(Log::class) ? LaravelLogger::class : BugsnagLogger::class);
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
                        $config = Mockery::mock();
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
