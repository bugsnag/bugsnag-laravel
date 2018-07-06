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
    return 'success';
});

Route::get('handled', function () {
    app('bugsnag')->notifyException(new Exception('Example exception!'));
});

Route::get('unhandled', function () {
    throw new Exception('Example exception!');
});
