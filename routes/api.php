<?php

use App\Enums\Roles;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => ['web', 'auth', 'csrf']], function () { // Enable session state etc. for certain API routes.

    Route::any('/logout', 'UserController@logout');

    Route::get('/me', 'UserController@getByAuthenticated');

    Route::get('/advice', 'AdviceController@index');
    Route::get('/news', 'NewsController@index');

    Route::get('/students/creditsexpected', 'StudentController@getCreditsExpected');

    Route::group(['middleware' => 'role:' . Roles::Student], function () {

        // These routes are only available to students, as they would fail to return anything for other roles.

        Route::get('/me/student', 'StudentController@getByAuthenticated');
        Route::patch('/me/student', 'StudentController@updatePartialByAuthenticated');
    });

    Route::group(['middleware' => 'role:' . Roles::StudyAdviser . ',' . Roles::Administrator], function () {

        Route::post('/advice', 'AdviceController@create');

        Route::get('/advice/{id}', 'AdviceController@getById');
        Route::patch('/advice/{id}', 'AdviceController@updatePartialById');
        Route::delete('/advice/{id}', 'AdviceController@deleteById');

        Route::post('/news', 'NewsController@create');

        Route::get('/news/{id}', 'NewsController@getById');
        Route::patch('/news/{id}', 'NewsController@updatePartialById');
        Route::delete('/news/{id}', 'NewsController@deleteById');

        Route::get('/students', 'StudentController@index');

        Route::get('/students/{id}', 'StudentController@getById');
    });

    Route::group(['middleware' => 'role:' . Roles::Administrator], function () {

        Route::patch('/students', 'StudentController@updatePartialByParameters');
        Route::delete('/students', 'StudentController@deleteByParameters');

        Route::patch('/students/{id}', 'StudentController@updatePartialById');
        Route::delete('/students/{id}', 'StudentController@deleteById');

        Route::post('/import/students', 'StudentController@createByImport');
    });
});