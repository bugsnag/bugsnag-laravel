<?php

namespace Bugsnag\BugsnagLaravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Bugsnag\Client make(string|null $apiKey, string|null $endpoint, bool $defaults)
 * @method static \GuzzleHttp\ClientInterface make(string|null $base, array $options)
 * @method static string|false getCaBundlePath()
 * @method static \Bugsnag\Configuration getConfig()
 * @method static $this registerCallback(callable $callback)
 * @method static $this registerDefaultCallbacks()
 * @method static void leaveBreadcrumb(string $name, string|null $type, array $metaData)
 * @method static void clearBreadcrumbs()
 * @method static void notifyException(\Throwable $throwable, callable|null $callback)
 * @method static void notifyError(string $name, string $message, callable|null $callback)
 * @method static void notify(\Bugsnag\Report $report, callable|null $callback)
 * @method static void deploy(string|null $repository, string|null $branch, string|null $revision)
 * @method static void build(string|null $repository, string|null $revision, string|null $provider, string|null $builderName)
 * @method static void flush()
 * @method static void startSession()
 * @method static \Bugsnag\SessionTracker getSessionTracker()
 */
class Bugsnag extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'bugsnag';
    }
}
