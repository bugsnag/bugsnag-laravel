<?php

namespace Bugsnag\BugsnagLaravel\Tests\Request;

use Bugsnag\BugsnagLaravel\Request\LaravelRequest;
use Bugsnag\BugsnagLaravel\Request\LaravelResolver;
use Bugsnag\Request\ConsoleRequest;
use Bugsnag\Request\RequestInterface;
use GrahamCampbell\TestBenchCore\MockeryTrait;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Mockery;
use PHPUnit_Framework_TestCase as TestCase;

class LaravelRequestTest extends TestCase
{
    use MockeryTrait;

    public function testCanResolveConsoleRequest()
    {
        $request = Mockery::mock(Request::class);
        $app = Mockery::mock(Application::class);

        $app->shouldReceive('runningInConsole')->once()->andReturn(true);
        $app->shouldReceive('make')->once()->with(Request::class)->andReturn($request);
        $request->shouldReceive('server')->once()->with('argv', [])->andReturn('test mock console command');

        $resolver = new LaravelResolver($app);
        $result = $resolver->resolve();

        $this->assertInstanceOf(RequestInterface::class, $result);
        $this->assertInstanceOf(ConsoleRequest::class, $result);
    }

    public function testCanResolveLaravelRequest()
    {
        $resolver = new LaravelResolver($app = Mockery::mock(Application::class));

        $app->shouldReceive('runningInConsole')->once()->andReturn(false);
        $app->shouldReceive('make')->once()->with(Request::class)->andReturn($request = Mockery::mock(Request::class));

        $request = $resolver->resolve();

        $this->assertInstanceOf(RequestInterface::class, $request);
        $this->assertInstanceOf(LaravelRequest::class, $request);
    }
}
