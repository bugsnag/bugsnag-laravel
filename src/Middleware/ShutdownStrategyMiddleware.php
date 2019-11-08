<?php

namespace Bugsnag\BugsnagLaravel\Middleware;

use Bugsnag\Client;
use Bugsnag\Shutdown\ShutdownStrategyInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\TerminableInterface;

/**
 * A Laravel middleware that, via the TerminableInterface, triggers the Client::flush() call when the Laravel Kernel
 * shuts down.
 */
class ShutdownStrategyMiddleware implements TerminableInterface, ShutdownStrategyInterface
{
    /**
     * @var \Bugsnag\Client $client
     */
    private $client;

    /**
     * A no-op handle(). This function is required for a Laravel middleware object (strange there's no interface for it though)
     *
     * @param $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        return $next($request);
    }

    /**
     * Called by the Bugsnag\Client constructor
     * @param \Bugsnag\Client $client
     */
    public function registerShutdownStrategy(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Called when the HTTP response has been sent and the Kernel is terminating
     * @param Request $request
     * @param Response $response
     */
    public function terminate(Request $request, Response $response)
    {
        // Flush any requests to Bugsnag
        if ($this->client) {
            $this->client->flush();
        }
    }
}
