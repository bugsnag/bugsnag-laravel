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

Route::get('unhandled_exception', function () {
    throw new Exception('Crashing exception!');
});

Route::get('unhandled_error', function () {
    call_foo();
});

Route::get('handled_exception', function () {
    Bugsnag::notifyException(new Exception('Handled exception!'));
});

Route::get('handled_error', function () {
    Bugsnag::notifyError('Handled Error', 'This is a handled error');
});

Route::get('/unhandled_controller_exception', 'TestController@unhandledException');
Route::get('/unhandled_controller_error', 'TestController@unhandledError');
Route::get('/handled_controller_exception', 'TestController@handledException');
Route::get('/handled_controller_error', 'TestController@handledError');

Route::get('/unhandled_middleware_exception', function () {
})->middleware('unMidEx');
Route::get('/unhandled_middleware_error', function () {
})->middleware('unMidErr');
Route::get('/handled_middleware_exception', function () {
})->middleware('hanMidEx');
Route::get('/handled_middleware_error', function () {
})->middleware('hanMidErr');

Route::view('/unhandled_view_exception', 'unhandledexception');
Route::view('/unhandled_view_error', 'unhandlederror');
Route::view('/unhandled_view_exception', 'handledexception');
Route::view('/unhandled_view_error', 'handlederror');
