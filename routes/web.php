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

Route::get('/logout', function (Request $request) {

    $request->session()->flush();
});

Route::get('/me', function (Request $request) {

    if (!$request->session()->get('authenticated'))
        abort(401, 'No authenticated LTI session.');

    return $request->session()->get('user');
});

Route::get('/', function (Request $request) {

    if (!$request->session()->get('authenticated'))
        abort(401, 'No authenticated LTI session.');

    return view('index');
});

function someLibraryFunction() {

    $request = request();
    $request->session()->put('foo', 'bar');

    // This does work: echo redirect('/get');

    header('Location: /get');
    exit;
};

Route::get('/set', function (Request $request) {

    someLibraryFunction();
});

Route::get('/get', function (Request $request) {

    print_r($request->session()->all());
    $request->session()->flush();
});