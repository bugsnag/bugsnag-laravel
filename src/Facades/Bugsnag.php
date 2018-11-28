<?php

namespace Bugsnag\BugsnagLaravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void build(string $repository = null, string $revision = null, string $provider = null, string $builderName = null)
 * @method static void clearBreadcrumbs()
 * @method static void deploy(string $repository = null, string $branch = null, string $revision = null)
 * @method static void flush()
 * @method static \Bugsnag\Configuration getConfig()
 * @method static \Bugsnag\Pipeline getPipeline()
 * @method static \Bugsnag\SessionTracker getSessionTracker()
 * @method static void leaveBreadcrumb(string $name, string $type = null, array $metaData = [])
 * @method static void notify(\Bugsnag\Report $report, callable $callback = null)
 * @method static void notifyError(string $name, string $message, callable $callback = null)
 * @method static void notifyException($throwable, callable $callback = null)
 * @method static \Bugsnag\Client registerDefaultCallbacks()
 * @method static \Bugsnag\Client registerCallback(callable $callback)
 * @method static \Bugsnag\Client registerMiddleware(callable $middleware)
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
