<?php

namespace Bugsnag\BugsnagLaravel\Middleware;

use PHPUnit\Framework\TestCase;
use Bugsnag\Report;
use Bugsnag\Configuration;
use Exception;
use Bugsnag\BugsnagLaravel\Middleware\UnhandledState;

global $currentBacktrace;

static $SUCCESSFUL_BACKTRACE = [
    [
        'class' => "Illuminate\\Foundation\\Exceptions\\Handler",
        'function' => 'report'
    ],
    [
        'class' => "empty"
    ],
    [
        'class' => "empty"
    ],
    [
        'class' => "Illuminate\\Something"
    ],
    [
        'class' => "Illuminate\\SomethingElse"
    ]
];

static $LEVEL_1_FAILURE = [

];

static $LEVEL_2_FAILURE = [

];

static $LEVEL_3_FAILURE = [

];

function debug_backtrace($options)
{
    global $currentBacktrace;
    return $currentBacktrace;
}

class UnhandledStateTest extends TestCase
{
    public function testNo()
    {
        global $currentBacktrace;
        $config = new Configuration('API-KEY');
        $report = Report::fromPHPThrowable(
            $config,
            new Exception("Oh no")
        );
        $report->setUnhandled(false);

        $currentBacktrace = $SUCCESSFUL_BACKTRACE;

        $middleware = new UnhandledState();
        $middleware($report);
        $this->assertSame(true, $report->getUnhandled());

    }
}