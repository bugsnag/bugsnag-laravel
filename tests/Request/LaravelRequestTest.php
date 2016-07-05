<?php

namespace Bugsnag\BugsnagLaravel\Tests\Request;

use Bugsnag\BugsnagLaravel\Request\LaravelRequest;
use Bugsnag\BugsnagLaravel\Request\LaravelResolver;
use Bugsnag\Request\NullRequest;
use Bugsnag\Request\RequestInterface;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Mockery;
use PHPUnit_Framework_TestCase as TestCase;

class LaravelRequestTest extends TestCase
{
    public function testCanResolveNullRequest()
    {
        $resolver = new LaravelResolver($app = Mockery::mock(Application::class));

        $app->shouldReceive('runningInConsole')->once()->andReturn(true);

        $request = $resolver->resolve();

        $this->assertInstanceOf(RequestInterface::class, $request);
        $this->assertInstanceOf(NullRequest::class, $request);
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
