<?php

namespace Bugsnag\BugsnagLaravel\Request;

use Bugsnag\Request\ResolverInterface;
use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Request;

class LaravelResolver implements ResolverInterface
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $app;

    /**
     * Create a new laravel request resolver instance.
     *
     * @param \Illuminate\Contracts\Container\Container $app
     *
     * @return void
     */
    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    /**
     * Resolve the current request.
     *
     * @return \Bugsnag\Request\RequestInterface
     */
    public function resolve()
    {

        $request = $this->app->make(Request::class);
        
        if ($this->app->runningInConsole()) {
            return new ConsoleRequest($request);
        }

        return new LaravelRequest($request);
    }
}
