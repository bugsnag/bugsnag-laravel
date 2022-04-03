<?php

namespace Bugsnag\BugsnagLaravel\Testing;

use Bugsnag\HttpClient;
use Bugsnag\Report;

class HttpClientFake extends HttpClient
{
    public function sendEvents()
    {
        // Do nothing!
    }

    public function sendBuildReport(array $buildInfo)
    {
        // Do nothing!
    }

    /**
     * @return \Illuminate\Support\Collection<Report>|Report[]
     */
    public function getQueue()
    {
        return collect($this->queue);
    }
}
