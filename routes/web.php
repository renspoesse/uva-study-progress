<?php

use DB;
use Illuminate\Http\Request;
use IMSGlobal\LTI\ToolProvider\DataConnector;
use IMSGlobal\LTI\ToolProvider\User;

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

//Route::any('/lti/launch/testadmin', 'LTIController@testAdmin');
//Route::any('/lti/launch/teststudent', 'LTIController@testStudent');
//Route::any('/lti/launch/teststudyadvisor', 'LTIController@testStudyAdvisor');

Route::any('/lti/launch', 'LTIController@index');

Route::any('/logout', function (Request $request) {

    $request->session()->flush();
});

Route::get('/me', function (Request $request) {

    if (!$request->session()->get('authenticated'))
        abort(401, 'No authenticated LTI session.');

    $connector = DataConnector\DataConnector::getDataConnector('', DB::connection()->getPdo());
    $user = User::fromRecordId($request->session()->get('record_id'), $connector);

    return array_merge($request->session()->get('user'), [

        'userId' => $user->getId()
    ]);
});

Route::get('/', function (Request $request) {

    return view('index');
});