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
    Route::get('/me/student', 'StudentController@getByAuthenticated');

    Route::get('/advice', 'AdviceController@index');
    Route::get('/news', 'NewsController@index');

    Route::group(['middleware' => 'role:' . Roles::Instructor . ',' . Roles::Administrator], function () {

        Route::post('/advice', 'AdviceController@create');

        Route::get('/advice/{id}', 'AdviceController@getById');
        Route::patch('/advice/{id}', 'AdviceController@updatePartialById');
        Route::delete('/advice/{id}', 'AdviceController@deleteById');

        Route::post('/news', 'NewsController@create');

        Route::get('/news/{id}', 'NewsController@getById');
        Route::patch('/news/{id}', 'NewsController@updatePartialById');
        Route::delete('/news/{id}', 'NewsController@deleteById');
    });

    Route::group(['middleware' => 'role:' . Roles::Administrator], function () {

        Route::get('/students', 'StudentController@index');

        Route::get('/students/{id}', 'StudentController@getById');
        Route::delete('/students/{id}', 'StudentController@deleteById');

        Route::post('/import/students', 'StudentController@createByImport');
    });
});