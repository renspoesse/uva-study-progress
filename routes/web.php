<?php

use Illuminate\Http\Request;
use App\Enums\Roles;

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

//Route::any('/lti/launch/testadmin', 'LtiController@testAdmin');
//Route::any('/lti/launch/teststudent', 'LtiController@testStudent');
//Route::any('/lti/launch/teststudyadvisor', 'LtiController@testStudyAdvisor');

Route::get('/', function () { return view('index'); });
Route::any('/lti/launch', 'LtiController@index');

Route::group(['middleware' => 'auth'], function () {

    Route::any('/logout', 'UserController@logout');
    Route::get('/me', 'UserController@getByAuthenticated');

    Route::get('/students', 'StudentController@index')->middleware('role:' . Roles::Administrator);

    Route::post('/import/students', 'StudentController@createByImport')->middleware('permission:' . Roles::Administrator);
});