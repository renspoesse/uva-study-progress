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

    Route::get('/advice', 'AdviceController@index')->middleware('role:' . Roles::Instructor);
    Route::post('/advice', 'AdviceController@create')->middleware('role:' . Roles::Instructor);

    Route::get('/advice/{id}', 'AdviceController@getById')->middleware('role:' . Roles::Instructor);
    Route::patch('/advice/{id}', 'AdviceController@updatePartialById')->middleware('role:' . Roles::Instructor);
    Route::delete('/advice/{id}', 'AdviceController@deleteById')->middleware('role:' . Roles::Instructor);

    Route::get('/news', 'NewsController@index')->middleware('role:' . Roles::Instructor);
    Route::post('/news', 'NewsController@create')->middleware('role:' . Roles::Instructor);

    Route::get('/news/{id}', 'NewsController@getById')->middleware('role:' . Roles::Instructor);
    Route::patch('/news/{id}', 'NewsController@updatePartialById')->middleware('role:' . Roles::Instructor);
    Route::delete('/news/{id}', 'NewsController@deleteById')->middleware('role:' . Roles::Instructor);

    Route::get('/students', 'StudentController@index')->middleware('role:' . Roles::Administrator);

    Route::get('/students/{id}', 'StudentController@getById')->middleware('role:' . Roles::Administrator);
    Route::delete('/students/{id}', 'StudentController@deleteById')->middleware('role:' . Roles::Administrator);

    Route::post('/import/students', 'StudentController@createByImport')->middleware('permission:' . Roles::Administrator);
});