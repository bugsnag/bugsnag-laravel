<?php

namespace Bugsnag\BugsnagLaravel\Tests\Stubs;

use RuntimeException;

class DebugBacktraceStub
{
    private static $backtrace = [];

    public static function get()
    {
        if (self::$backtrace === []) {
            throw new RuntimeException('No backtrace was set!');
        }

        $backtrace = self::$backtrace;
        self::clear();

        return $backtrace;
    }

    public static function set(array $backtrace)
    {
        self::$backtrace = $backtrace;
    }

    public static function clear()
    {
        self::$backtrace = [];
    }
}
