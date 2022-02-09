<?php

namespace Bugsnag\BugsnagLaravel\Tests\Stubs;

use Bugsnag\BugsnagLaravel\LaravelLogger;
use Bugsnag\BugsnagLaravel\MultiLogger;
use Bugsnag\BugsnagLaravel\Queue\Tracker;
use Bugsnag\Client;

class InjecteeWithLogInterface
{
    private $client;
    private $tracker;
    private $multiLogger;
    private $laravelLogger;

    public function __construct(
        Client $client,
        Tracker $tracker,
        MultiLogger $multiLogger,
        LaravelLogger $laravelLogger
    ) {
        $this->client = $client;
        $this->tracker = $tracker;
        $this->multiLogger = $multiLogger;
        $this->laravelLogger = $laravelLogger;
    }

    public function wasConstructed()
    {
        return $this->client instanceof Client
            && $this->tracker instanceof Tracker
            && $this->multiLogger instanceof MultiLogger
            && $this->laravelLogger instanceof LaravelLogger;
    }
}
