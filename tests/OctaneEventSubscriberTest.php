<?php

namespace Bugsnag\BugsnagLaravel\Tests;

use Bugsnag\BugsnagLaravel\OctaneEventSubscriber;
use Bugsnag\BugsnagLaravel\Queue\Tracker;
use Bugsnag\Client;
use ReflectionMethod;

class OctaneEventSubscriberTest extends AbstractTestCase
{
    /**
     * Simulate the state a worker is left in after dispatchSync() runs a job:
     * queue->before sets the Tracker and changes the fallback type to 'Queue'.
     * cleanup() must undo both so subsequent HTTP reports on the same worker
     * are not poisoned with queue context.
     */
    public function testCleanupClearsTrackerAndResetsFallbackType(): void
    {
        /** @var Tracker $tracker */
        $tracker = $this->app->make(Tracker::class);
        /** @var Client $client */
        $client = $this->app->make(Client::class);

        $tracker->set([
            'name' => 'App\\Jobs\\SomeJob',
            'queue' => 'default',
            'attempts' => 1,
            'connection' => 'sync',
            'resolved' => 'App\\Jobs\\SomeJob',
        ]);
        $client->setFallbackType('Queue');

        $this->assertNotNull($tracker->get(), 'Precondition: tracker should hold job state before cleanup');
        $this->assertSame('Queue', $client->getConfig()->getAppData()['type'], 'Precondition: fallback type should be Queue before cleanup');

        $cleanup = new ReflectionMethod(OctaneEventSubscriber::class, 'cleanup');
        $cleanup->setAccessible(true);
        $cleanup->invoke(new OctaneEventSubscriber());

        $this->assertNull($tracker->get(), 'Tracker should be cleared after cleanup');
        $this->assertNotSame('Queue', $client->getConfig()->getAppData()['type'], 'Fallback type should no longer be Queue after cleanup');
    }
}
