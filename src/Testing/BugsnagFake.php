<?php

namespace Bugsnag\BugsnagLaravel\Testing;

use Bugsnag\Client;
use Bugsnag\Report;
use GuzzleHttp\ClientInterface;
use Illuminate\Support\Collection;
use Mockery;
use PHPUnit\Framework\Assert as PHPUnit;

class BugsnagFake extends Client
{
    /** @var HttpClientFake */
    protected $http;

    /**
     * @param  Client  $client
     * @return BugsnagFake
     */
    public static function fromClient(Client $client)
    {
        $instance = new self(
            $client->getConfig(),
            $client->resolver
        );

        // Use a Fake HttpClient that doesn't dispatch
        // the Queue and that has a Queue getter.
        $instance->http = new HttpClientFake(
            $client->getConfig(),
            Mockery::mock(ClientInterface::class)
        );

        return $instance;
    }

    /**
     * Get all the reports matching a truth-test callback.
     *
     * @param  string  $name
     * @param  callable(Report): bool|null  $callback
     * @return Collection
     */
    public function notified($name, $callback = null)
    {
        $callback = $callback ?: function () {
            return true;
        };

        return collect($this->http->getQueue())
            ->filter(function (Report $report) use ($name) {
                return $report->getName() === $name;
            })
            ->filter(function (Report $report) use ($callback) {
                return $callback($report);
            });
    }


    /**
     * Assert if a report was notified based on a truth-test callback.
     *
     * @param  string  $reportName
     * @param  callable(Report): bool|null  $callback
     * @return void
     */
    public function assertNotified($reportName, $callback = null)
    {
        PHPUnit::assertTrue(
            $this->notified($reportName, $callback)->isNotEmpty(),
            "The expected [{$reportName}] report was not notified."
        );
    }

    /**
     * Assert if a report was pushed a number of times.
     *
     * @param  string  $reportName
     * @param  int  $times
     * @param  callable(Report): bool|int|null  $callback
     * @return void
     */
    public function assertNotifiedTimes($reportName, $times = 1, $callback = null)
    {
        $count = $this->notified($reportName, $callback)->count();

        PHPUnit::assertSame(
            $times, $count,
            "The expected [{$reportName}] bugsnag was reported {$count} times instead of {$times} times."
        );
    }

    /**
     * Determine if a report was notified based on a truth-test callback.
     *
     * @param  string  $reportName
     * @param  callable(Report): bool|null  $callback
     * @return void
     */
    public function assertNotNotified($reportName, $callback = null)
    {
        PHPUnit::assertTrue(
            $this->notified($reportName, $callback)->count() === 0 &&
            "The unexpected [{$reportName}] report was notified."
        );
    }

    /**
     * Assert that no bugsnag reports were notified.
     *
     * @return void
     */
    public function assertNothingNotified()
    {
        PHPUnit::assertEmpty($this->http->getQueue(), 'Bugsnags were notified unexpectedly.');
    }
}
