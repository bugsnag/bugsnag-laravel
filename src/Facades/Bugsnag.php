<?php

namespace Bugsnag\BugsnagLaravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Bugsnag\BugsnagLaravel\Facades\Bugsnag setAppVersion(string $appVersion)
 * @method static \Bugsnag\BugsnagLaravel\Facades\Bugsnag setAppType(string $type)
 * @method static \Bugsnag\BugsnagLaravel\Facades\Bugsnag setReleaseStage(string $releaseStage)
 * @method static \Bugsnag\BugsnagLaravel\Facades\Bugsnag setFallbackType(string $type)
 * @method static \Bugsnag\BugsnagLaravel\Facades\Bugsnag setNotifier(array $notifier)
 * @method static \Bugsnag\BugsnagLaravel\Facades\Bugsnag setHostname(string $hostname)
 * @method static \Bugsnag\BugsnagLaravel\Facades\Bugsnag registerCallback(callable $callback)
 * @method static \Bugsnag\BugsnagLaravel\Facades\Bugsnag function registerDefaultCallbacks()
 * @method static \Bugsnag\BugsnagLaravel\Facades\Bugsnag registerMiddleware(callable $middleware)
 * @method static void leaveBreadcrumb(string $name, string $type = null, array $metaData = [])
 * @method static void clearBreadcrumbs()
 * @method static void notifyException(\Throwable $throwable, callable $callback = null)
 * @method static void notifyError(string $name, string $message, callable $callback = null)
 * @method static void notify(\Bugsnag\Report $report, callable $callback = null)
 * @method static void deploy(string $repository = null, string $branch = null, string $revision = null)
 * @method static void build(string $repository = null, string $revision = null, string $provider = null, string $builderName = null)
 * @method static void flush()
 * @method static void startSession()
 *
 * @see \Bugsnag\Client
 */
class Bugsnag extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'bugsnag';
    }
}
