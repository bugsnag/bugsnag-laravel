<?php

namespace Bugsnag\BugsnagLaravel\Tests;

use Bugsnag\BugsnagLaravel\Facades\Bugsnag;
use Bugsnag\Report;
use PHPUnit\Framework\ExpectationFailedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FakeTest extends AbstractTestCase
{
    public function testFakeShouldInterceptNotifyErrorCorrectly()
    {
        Bugsnag::fake();

        Bugsnag::notifyError('Hi!', 'This is an example error.');

        $this->assertEquals(
            1,
            Bugsnag::notified('Hi!')->count(),
            'Event was notified'
        );
    }

    public function testFakeShouldInterceptNotifyExceptionCorrectly()
    {
        Bugsnag::fake();

        Bugsnag::notifyException(new NotFoundHttpException('oops'));

        $this->assertEquals(
            1,
            Bugsnag::notified(NotFoundHttpException::class, function (Report $report) {
                return $report->getMessage() === 'oops';
            })->count(),
            'Event was notified'
        );
    }

    public function testFakeShouldInterceptNotifyCorrectly()
    {
        Bugsnag::fake();

        Bugsnag::notify(
            Report::fromNamedError(Bugsnag::getConfig(), 'This is my name!', 'my message')
        );

        $this->assertEquals(
            1,
            Bugsnag::notified('This is my name!', function (Report $report) {
                return $report->getMessage() === 'my message';
            })->count(),
            'Event should be notified'
        );

        $this->assertEquals(
            0,
            Bugsnag::notified('This is my name!', function (Report $report) {
                return $report->getMessage() === 'Different message';
            })->count(),
            'Non-existent event should not be notified'
        );
    }

    public function testShouldAssertNothingWasNotified()
    {
        Bugsnag::fake();

        Bugsnag::assertNothingNotified();

        Bugsnag::notifyError('Hi!', 'This is an example error.');

        $this->expectException(ExpectationFailedException::class);

        Bugsnag::assertNothingNotified();
    }

    public function testShouldAssertNotNotified()
    {
        Bugsnag::fake();

        Bugsnag::assertNotNotified('Hi!');

        Bugsnag::notifyError('Hi!', 'This is an example error.');

        $this->expectException(ExpectationFailedException::class);

        Bugsnag::assertNotNotified('Hi!');
    }

    public function testShouldAssertNotifiedTimes()
    {
        Bugsnag::fake();

        Bugsnag::assertNotifiedTimes('Hi!', 0);

        Bugsnag::notifyError('Hi!', 'Foo');
        Bugsnag::notifyError('Hi!', 'Foo');

        Bugsnag::assertNotifiedTimes('Hi!', 2);

        Bugsnag::notifyError('Hi!', 'Bar');

        Bugsnag::assertNotifiedTimes('Hi!', 3);

        Bugsnag::assertNotifiedTimes('Hi!', 2, function (Report $report) {
            return $report->getMessage() === 'Foo';
        });
        Bugsnag::assertNotifiedTimes('Hi!', 1, function (Report $report) {
            return $report->getMessage() === 'Bar';
        });

        $this->expectException(ExpectationFailedException::class);

        // Make sure it breaks when the times is not correct.

        Bugsnag::assertNotifiedTimes('Hi!', 2, function (Report $report) {
            return $report->getMessage() === 'Bar';
        });
    }

    public function testShouldAssertNotified()
    {
        Bugsnag::fake();

        Bugsnag::notifyError('Hi!', 'Foo');

        Bugsnag::assertNotified('Hi!');

        Bugsnag::notifyError('Hi!', 'Foo');

        // Should work even when tracking twice.
        Bugsnag::assertNotified('Hi!');

        Bugsnag::notifyError('Hi!', 'Bar');

        Bugsnag::assertNotified('Hi!', function (Report $report) {
            return $report->getMessage() === 'Bar';
        });

        $this->expectException(ExpectationFailedException::class);

        // Make sure it breaks when the times is not correct.

        Bugsnag::assertNotified('Hi!', function (Report $report) {
            return $report->getMessage() === 'Bar FALSE';
        });
    }
}
