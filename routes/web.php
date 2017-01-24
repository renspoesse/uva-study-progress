<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::any('/lti/launch', 'LTIController@index');

Route::any('/logout', function (Request $request) {

    $request->session()->flush();
});

Route::get('/me', function (Request $request) {

    if (!$request->session()->get('authenticated'))
        abort(401, 'No authenticated LTI session.');

    return $request->session()->get('user');
});

Route::get('/', function (Request $request) {

    return view('index');
});