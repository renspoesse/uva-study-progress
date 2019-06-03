<?php

use Illuminate\Http\Request;
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

// Enable session state etc. for certain API routes.
Route::group(['middleware' => ['web', 'auth', 'csrf', 'last.seen']],
    function () {
        Route::any('/logout', 'UserController@logout');

        Route::get('/me', 'UserController@getByAuthenticated');

        Route::get('/advice', 'AdviceController@index');
        Route::get('/news', 'NewsController@index');

        Route::get('/students/creditsaverage', 'StudentController@getCreditsAverage');
        // Route::get('/students/programsatisfactionaverage', 'StudentController@getProgramSatisfactionAverage');

        // These routes are only available to students, as they would fail to return anything for other roles.
        Route::group(['middleware' => 'role:' . Roles::Student], function () {
            Route::get('/me/student', 'StudentController@getByAuthenticated');
            Route::patch('/me/student', 'StudentController@updatePartialByAuthenticated');
        });

        Route::group(['middleware' => 'role:' . Roles::StudyAdviser . ',' . Roles::Administrator], function () {
            Route::post('/advice', 'AdviceController@create');

            Route::get('/advice/{id}', 'AdviceController@getById');
            Route::patch('/advice/{id}', 'AdviceController@updatePartialById');
            Route::delete('/advice/{ids}', 'AdviceController@deleteByIds');

            Route::post('/news', 'NewsController@create');

            Route::get('/news/{id}', 'NewsController@getById');
            Route::patch('/news/{id}', 'NewsController@updatePartialById');
            Route::delete('/news/{ids}', 'NewsController@deleteByIds');

            Route::get('/students', 'StudentController@index');

            Route::get('/students/{id}', 'StudentController@getById');
        });

        Route::group(['middleware' => 'role:' . Roles::Administrator], function () {
            Route::get('/settings', 'SettingsController@get');
            Route::patch('/settings', 'SettingsController@updatePartial');

            Route::patch('/students', 'StudentController@updatePartialByParameters');
            Route::delete('/students', 'StudentController@deleteByParameters');

            Route::patch('/students/{ids}', 'StudentController@updatePartialByIds');
            Route::delete('/students/{ids}', 'StudentController@deleteByIds');

            Route::get('/export/students', 'StudentController@export');

            Route::post('/import/students', 'StudentController@createByImport');
        });
    });
