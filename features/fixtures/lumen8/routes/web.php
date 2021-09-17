<?php

/** @var \Laravel\Lumen\Routing\Router $router */

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

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get('unhandled_exception', function () {
    throw new Exception('Crashing exception!');
});

$router->get('unhandled_error', function () {
    call_foo();
});

$router->get('handled_exception', function () {
    Bugsnag::notifyException(new Exception('Handled exception!'));
});

$router->get('handled_error', function () {
    Bugsnag::notifyError('Handled Error', 'This is a handled error');
});

$router->get('/unhandled_controller_exception', 'TestController@unhandledException');
$router->get('/unhandled_controller_error', 'TestController@unhandledError');
$router->get('/handled_controller_exception', 'TestController@handledException');
$router->get('/handled_controller_error', 'TestController@handledError');

$router->group(['middleware' => 'unhandledMiddlewareException'], function () use ($router) {
    $router->get('/unhandled_middleware_exception', function () {});
});

$router->group(['middleware' => 'unhandledMiddlewareError'], function () use ($router) {
    $router->get('/unhandled_middleware_error', function () {});
});

$router->group(['middleware' => 'handledMiddlewareException'], function () use ($router) {
    $router->get('/handled_middleware_exception', function () {});
});

$router->group(['middleware' => 'handledMiddlewareError'], function () use ($router) {
    $router->get('/handled_middleware_error', function () {});
});

$router->get('/unhandled_view_exception', function () {
    return view('unhandledexception');
});

$router->get('/unhandled_view_error', function () {
    return view('unhandlederror');
});

$router->get('/handled_view_exception', function () {
    return view('handledexception');
});

$router->get('/handled_view_error', function () {
    return view('handlederror');
});

/**
 * Return some diagnostics if an OOM did not happen when it should have.
 *
 * @return string
 */
function noOomResponse() {
    $limit = ini_get('memory_limit');
    $memory = var_export(memory_get_usage(), true);
    $peak = var_export(memory_get_peak_usage(), true);

    return <<<HTML
        No OOM!
        {$limit}
        {$memory}
        {$peak}
    HTML;
}

$router->get('/oom/big', function () {
    $a = str_repeat('a', 2147483647);

    return noOomResponse();
});

$router->get('/oom/small', function () {
    ini_set('memory_limit', memory_get_usage() + (1024 * 1024 * 2));
    ini_set('display_errors', true);

    $i = 0;

    gc_disable();

    while ($i++ < 12345678) {
        $a = new stdClass;
        $a->b = $a;
    }

    return noOomResponse();
});
