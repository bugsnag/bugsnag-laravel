<?php

use Bugsnag\Report;
use Bugsnag\BugsnagLaravel\Facades\Bugsnag;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the 'web' middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('crash', function () {
    throw new Exception('Crashing exception!');
});

Route::get('notify', function () {
    Bugsnag::notifyException(new Exception('A notified exception'));

    return 'An exception has been notified.  Check your dashboard to view it!';
});

Route::get('metadata', function () {
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

Route::get('notify-with-metadata', function () {
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

Route::get('severity', function () {
    Bugsnag::notifyException(
        new Exception('A notified exception with a severity of "info"'),
        function(Report $report) {
            $report->setSeverity('info');
        }
    );

    return 'An exception has been notified.  Check your dashboard to view it!';
});
