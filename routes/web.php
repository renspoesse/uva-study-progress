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

Route::any('/lti/launch', 'LtiController@index');

/**
 * Catch all route to enable HTML5 history mode in VueRouter.
 */
Route::any('{all}', function () { return view('index'); })->where('all', '[\/\w\.-]*');