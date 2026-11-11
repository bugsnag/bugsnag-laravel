<?php

namespace Bugsnag\BugsnagLaravel\Internal;

use Bugsnag\Client;
use Bugsnag\Shutdown\ShutdownStrategyInterface;

/**
 * Flushes the client on shutdown without keeping it alive.
 *
 * The default strategy registers [$client, 'flush'] as a shutdown function,
 * which holds the client, and through it the application, until the process
 * exits. The client is owned by the container, which lives until shutdown, so
 * a weak reference is enough to flush it there, while an application that has
 * been discarded (e.g. between tests) can be garbage collected.
 *
 * @internal
 */
final class WeakShutdownStrategy implements ShutdownStrategyInterface
{
    /**
     * @var callable
     */
    private $registerShutdownFunction;

    /**
     * @param callable|null $registerShutdownFunction
     */
    public function __construct($registerShutdownFunction = null)
    {
        $this->registerShutdownFunction = $registerShutdownFunction ?: 'register_shutdown_function';
    }

    /**
     * @param \Bugsnag\Client $client
     *
     * @return void
     */
    public function registerShutdownStrategy(Client $client)
    {
        // WeakReference requires PHP 7.4+, so older versions keep the client alive as before
        if (!class_exists('WeakReference')) {
            call_user_func($this->registerShutdownFunction, [$client, 'flush']);

            return;
        }

        $reference = \WeakReference::create($client);

        call_user_func($this->registerShutdownFunction, static function () use ($reference) {
            $client = $reference->get();

            if ($client !== null) {
                $client->flush();
            }
        });
    }
}
