<?php

// stub out debug_backtrace in UnhandledState's namespace so we can test it

namespace Bugsnag\BugsnagLaravel\Middleware;

use Bugsnag\BugsnagLaravel\Tests\Stubs\DebugBacktraceStub;

function debug_backtrace()
{
    return DebugBacktraceStub::get();
}

namespace Bugsnag\BugsnagLaravel\Tests\Middleware;

use Bugsnag\BugsnagLaravel\Middleware\UnhandledState;
use Bugsnag\BugsnagLaravel\Tests\Stubs\DebugBacktraceStub;
use Bugsnag\Configuration;
use Bugsnag\Report;
use Exception;
use PHPUnit_Framework_TestCase as TestCase;

class UnhandledStateTest extends TestCase
{
    /**
     * @var Report
     */
    private $report;

    /**
     * @var \Closure
     */
    private $next;

    /**
     * @var bool
     */
    private $nextWasCalled;

    /**
     * @before
     */
    protected function beforeEach()
    {
        $this->report = Report::fromPHPThrowable(
            new Configuration('api-key'),
            new Exception('abc')
        );

        $this->nextWasCalled = false;
        $this->next = function (Report $report) {
            $this->assertSame($this->report, $report);
            $this->nextWasCalled = true;
        };
    }

    /**
     * @after
     */
    protected function afterEach()
    {
        DebugBacktraceStub::clear();

        $this->assertTrue($this->nextWasCalled);
    }

    /**
     * @dataProvider unhandledBacktraceProvider
     */
    public function testReportIsUnhandled(array $backtrace)
    {
        DebugBacktraceStub::set($backtrace);

        $this->assertFalse($this->report->getUnhandled());
        $this->assertSame(['type' => 'handledException'], $this->report->getSeverityReason());

        $unhandledState = new UnhandledState();
        $unhandledState->__invoke($this->report, $this->next);

        $this->assertTrue(
            $this->report->getUnhandled(),
            'Expected the report to be unhandled but it was handled!'
        );

        $this->assertSame(
            [
                'type' => 'unhandledExceptionMiddleware',
                'attributes' => ['framework' => 'Laravel'],
            ],
            $this->report->getSeverityReason()
        );
    }

    public function unhandledBacktraceProvider()
    {
        yield 'minimal backtrace' => [[
            // the backtrace must go through the Handler::report method
            ['class' => \Illuminate\Foundation\Exceptions\Handler::class, 'function' => 'report'],
            // then any class in the Illuminate namespace
            ['class' => \Illuminate\Something::class],
            // followed by any other class in the Illuminate namespace
            ['class' => \Illuminate\SomethingElse::class],
        ]];

        yield 'minimal backtrace (App exception handler)' => [[
            // the backtrace must go through the Handler::report method
            ['class' => \Illuminate\Foundation\Exceptions\Handler::class, 'function' => 'report'],
            // then through the app exception handler
            ['class' => \App\Exception\Handler::class],
            // followed by any other class in the Illuminate namespace
            ['class' => \Illuminate\SomethingElse::class],
        ]];

        yield 'backtrace with other classes' => [[
            ['class' => \SomeClass::class],
            ['class' => \Illuminate\Foundation\Exceptions\Handler::class, 'function' => 'report'],
            ['class' => \Some\OtherClass::class],
            ['class' => \Illuminate\Abc::class],
            ['class' => \Illuminate\AbcElse::class],
            ['class' => \Yet\AnotherClass::class],
        ]];

        yield 'backtrace with other classes (App exception handler)' => [[
            ['class' => \SomeClass::class],
            ['class' => \Illuminate\Foundation\Exceptions\Handler::class, 'function' => 'report'],
            ['class' => \Some\OtherClass::class],
            ['class' => \App\Exception\Handler::class],
            ['class' => \Illuminate\AbcElse::class],
            ['class' => \Yet\AnotherClass::class],
        ]];

        yield 'backtrace with non-class frames' => [[
            ['class' => \SomeClass::class],
            ['function' => 'a'],
            ['class' => \Illuminate\Foundation\Exceptions\Handler::class, 'function' => 'report'],
            ['function' => 'b'],
            ['function' => 'c'],
            ['class' => \Some\OtherClass::class],
            ['class' => \Illuminate\Abc::class],
            ['function' => 'x'],
            ['function' => 'y'],
            ['class' => \Illuminate\AbcElse::class],
            ['function' => 'z'],
            ['class' => \Yet\AnotherClass::class],
        ]];

        yield 'backtrace with non-class frames (App exception handler)' => [[
            ['class' => \SomeClass::class],
            ['function' => 'a'],
            ['class' => \Illuminate\Foundation\Exceptions\Handler::class, 'function' => 'report'],
            ['function' => 'b'],
            ['function' => 'c'],
            ['class' => \Some\OtherClass::class],
            ['class' => \App\Exception\Handler::class],
            ['function' => 'x'],
            ['function' => 'y'],
            ['class' => \Illuminate\AbcElse::class],
            ['function' => 'z'],
            ['class' => \Yet\AnotherClass::class],
        ]];
    }

    /**
     * @dataProvider handledBacktraceProvider
     */
    public function testReportIsHandled(array $backtrace)
    {
        DebugBacktraceStub::set($backtrace);

        $this->assertFalse($this->report->getUnhandled());
        $this->assertSame(['type' => 'handledException'], $this->report->getSeverityReason());

        $unhandledState = new UnhandledState();
        $unhandledState->__invoke($this->report, $this->next);

        $this->assertFalse($this->report->getUnhandled());
        $this->assertSame(['type' => 'handledException'], $this->report->getSeverityReason());
    }

    public function handledBacktraceProvider()
    {
        yield 'empty backtrace' => [[[]]];

        yield 'no illuminate exception handler' => [[
            ['class' => \NotIlluminate\Foundation\Exceptions\Handler::class, 'function' => 'report'],
            ['class' => \Illuminate\Something::class],
            ['class' => \Illuminate\SomethingElse::class],
        ]];

        yield 'illuminate exception handler but wrong method' => [[
            ['class' => \Illuminate\Foundation\Exceptions\Handler::class, 'function' => 'notReport'],
            ['class' => \Illuminate\Something::class],
            ['class' => \Illuminate\SomethingElse::class],
        ]];

        yield 'no illuminate exception handler (App exception handler)' => [[
            ['class' => \NotIlluminate\Foundation\Exceptions\Handler::class, 'function' => 'report'],
            ['class' => \App\Exception\Handler::class],
            ['class' => \Illuminate\SomethingElse::class],
        ]];

        yield 'illuminate exception handler but wrong method (App exception handler)' => [[
            ['class' => \Illuminate\Foundation\Exceptions\Handler::class, 'function' => 'notReport'],
            ['class' => \App\Exception\Handler::class],
            ['class' => \Illuminate\SomethingElse::class],
        ]];

        yield 'illuminate namespace not at the beginning' => [[
            ['class' => \Illuminate\Foundation\Exceptions\Handler::class, 'function' => 'report'],
            ['class' => \Abc\Illuminate\Something::class],
            ['class' => \Abc\Illuminate\SomethingElse::class],
        ]];

        yield 'illuminate namespace not at the beginning (App exception handler)' => [[
            ['class' => \Illuminate\Foundation\Exceptions\Handler::class, 'function' => 'report'],
            ['class' => \App\Exception\Handler::class],
            ['class' => \Abc\Illuminate\SomethingElse::class],
        ]];

        yield 'no consecutive Illuminate classes' => [[
            ['class' => \SomeClass::class],
            ['class' => \Illuminate\Foundation\Exceptions\Handler::class, 'function' => 'report'],
            ['class' => \Some\OtherClass::class],
            ['class' => \Illuminate\Abc::class],
            // this ensures the report is handled as there must be two
            // consecutive Illuminate frames
            ['class' => \AnotherClass::class],
            ['class' => \Illuminate\AbcElse::class],
            ['class' => \Yet\AnotherClass::class],
        ]];

        yield 'no consecutive Illuminate classes (App exception handler)' => [[
            ['class' => \SomeClass::class],
            ['class' => \Illuminate\Foundation\Exceptions\Handler::class, 'function' => 'report'],
            ['class' => \Some\OtherClass::class],
            ['class' => \App\Exception\Handler::class],
            // this ensures the report is handled as there must be two
            // consecutive Illuminate frames
            ['class' => \AnotherClass::class],
            ['class' => \Illuminate\AbcElse::class],
            ['class' => \Yet\AnotherClass::class],
        ]];
    }
}
