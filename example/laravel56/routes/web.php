<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
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
    return "An exception has been notified.  Check your dashboard to view it!";
});

Route::get('metadata', function () {
    Bugsnag::registerCallback(function($report) {
        $report->setMetaData([
            'routeDetails' => [
                'path' => 'metadata',
                'function' => 'Adds a callback to Bugsnag that adds metadata to a notification'
            ]
        ]);
    });
    throw new Exception('Metadata exception!');
});

Route::get('notifywithmetadata', function () {
    Bugsnag::notifyException(new Exception('A notified exception'), function($report) {
        $report->setMetaData([
            'routeDetails' => [
                'path' => 'notifywithmetadata',
                'function' => 'Shows how to send extra data with a manually notified exception!'
            ]
        ]);
    });
    return "An exception has been notified.  Check your dashboard to view it!";
});

Route::get('severity', function () {
    Bugsnag::notifyException(new Exception('A notified exception'), function($report) {
        $report->setSeverity("info");
    });
    return "An exception has been notified.  Check your dashboard to view it!";
});