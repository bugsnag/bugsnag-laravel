<?php

use Bugsnag\Report;
use Bugsnag\BugsnagLaravel\Facades\Bugsnag;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('crash', function () {
    throw new Exception('Crashing exception!');
});

$router->get('notify', function () {
    Bugsnag::notifyException(new Exception('A notified exception'));

    return 'An exception has been notified.  Check your dashboard to view it!';
});

$router->get('metadata', function () {
    Bugsnag::registerCallback(function(Report $report) {
        $report->setMetaData([
            'Route Details' => [
                'path' => 'metadata',
                'function' => 'Adds a callback to Bugsnag that adds metadata to a notification'
            ]
        ]);
    });

    throw new Exception('Metadata exception!');
});

$router->get('notify-with-metadata', function () {
    Bugsnag::notifyException(
        new Exception('A notified exception with metadata'),
        function(Report $report) {
            $report->setMetaData([
                'Route Details' => [
                    'path' => 'notify-with-metadata',
                    'function' => 'Shows how to send extra data with a manually notified exception!'
                ]
            ]);
        }
    );

    return 'An exception has been notified.  Check your dashboard to view it!';
});

$router->get('severity', function () {
    Bugsnag::notifyException(
        new Exception('A notified exception with a severity of "info"'),
        function(Report $report) {
            $report->setSeverity('info');
        }
    );

    return 'An exception has been notified.  Check your dashboard to view it!';
});

$router->get('/', function () use ($router) {
    $content = <<<HTML
<pre style="font-size: 20px;">{
    "Version": "{$router->app->version()}",
    "Bugsnag": {
        "Crash": "<a href="/crash">/crash</a>",
        "Notify": "<a href="/notify">/notify</a>",
        "Crash with added metadata": "<a href="/metadata">/metadata</a>",
        "Notify with added metadata": "<a href="/notify-with-metadata">/notify-with-metadata</a>",
        "Notify with modified severity": "<a href="/severity">/severity</a>"
    }
}</pre>
HTML;

    $response = response($content);

    return $response;;
});
