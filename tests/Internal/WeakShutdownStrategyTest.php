<?php

namespace Bugsnag\BugsnagLaravel\Tests\Internal;

use Bugsnag\BugsnagLaravel\Internal\WeakShutdownStrategy;
use Bugsnag\Client;
use Bugsnag\Configuration;
use PHPUnit_Framework_TestCase as TestCase;

class WeakShutdownStrategyTest extends TestCase
{
    public function testItFlushesTheClientOnShutdown()
    {
        $registered = [];
        $strategy = new WeakShutdownStrategy(function ($callback) use (&$registered) {
            $registered[] = $callback;
        });

        $client = $this->getMockBuilder(Client::class)->disableOriginalConstructor()->getMock();
        $client->expects($this->once())->method('flush');

        $strategy->registerShutdownStrategy($client);

        $this->assertCount(1, $registered);

        call_user_func($registered[0]);
    }

    public function testItDoesNotKeepTheClientAlive()
    {
        if (!class_exists('WeakReference')) {
            $this->markTestSkipped('WeakReference is only available on PHP 7.4+');
        }

        $registered = [];
        $strategy = new WeakShutdownStrategy(function ($callback) use (&$registered) {
            $registered[] = $callback;
        });

        $client = new Client(new Configuration('example-api-key'), null, null, $strategy);
        $reference = \WeakReference::create($client);

        unset($client);
        gc_collect_cycles();

        $this->assertNull($reference->get());
        $this->assertCount(1, $registered);

        call_user_func($registered[0]);
    }
}
