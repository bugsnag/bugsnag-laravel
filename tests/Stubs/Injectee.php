<?php

namespace Bugsnag\BugsnagLaravel\Tests\Stubs;

use Bugsnag\BugsnagLaravel\Queue\Tracker;
use Bugsnag\Client;
use Bugsnag\PsrLogger\BugsnagLogger;
use Bugsnag\PsrLogger\MultiLogger;

class Injectee
{
    private $client;
    private $tracker;
    private $multiLogger;
    private $bugsnagLogger;

    public function __construct(
        Client $client,
        Tracker $tracker,
        MultiLogger $multiLogger,
        BugsnagLogger $bugsnagLogger
    ) {
        $this->client = $client;
        $this->tracker = $tracker;
        $this->multiLogger = $multiLogger;
        $this->bugsnagLogger = $bugsnagLogger;
    }

    public function wasConstructed()
    {
        return $this->client instanceof Client
            && $this->tracker instanceof Tracker
            && $this->multiLogger instanceof MultiLogger
            && $this->bugsnagLogger instanceof BugsnagLogger;
    }
}
