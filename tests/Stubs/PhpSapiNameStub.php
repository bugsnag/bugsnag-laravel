<?php

namespace Bugsnag\BugsnagLaravel\Tests\Stubs;

class PhpSapiNameStub
{
    private static $name = 'cli';

    public static function get()
    {
        return self::$name;
    }

    public static function set($name)
    {
        self::$name = $name;
    }

    public static function reset()
    {
        self::$name = 'cli';
    }
}
