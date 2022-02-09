<?php

namespace Bugsnag\BugsnagLaravel\Tests\Facades;

use Bugsnag\BugsnagLaravel\Facades\Bugsnag;
use Bugsnag\BugsnagLaravel\Tests\AbstractTestCase;
use Bugsnag\Client;
use Illuminate\Support\Facades\Facade;

class BugsnagTest extends AbstractTestCase
{
    public function testIsFacade()
    {
        $this->assertInstanceOf(Facade::class, new Bugsnag());
    }

    public function testIsFacadeForBugsnagClient()
    {
        $root = Bugsnag::getFacadeRoot();

        $this->assertInstanceOf(Client::class, $root);
        $this->assertSame($root, $this->app->make('bugsnag'));
    }

    public function testItCanCallClientMethods()
    {
        Bugsnag::setDiscardClasses(['Example']);
        $this->assertSame(['Example'], Bugsnag::getDiscardClasses());

        Bugsnag::setNotifyEndpoint('https://example.com');
        $this->assertSame('https://example.com', Bugsnag::getNotifyEndpoint());

        $client = $this->app->make('bugsnag');

        $this->assertSame(['Example'], $client->getDiscardClasses());
        $this->assertSame('https://example.com', $client->getNotifyEndpoint());
    }
}
