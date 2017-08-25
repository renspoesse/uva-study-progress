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

Route::post('/lti/launch', 'LtiController@index');

// Route::get('/test', function (Request $request) {
//
//     \Illuminate\Support\Facades\Log::info('Request method: ' . $request->method());
//     \Illuminate\Support\Facades\Log::info('Request URL: ' . $request->fullUrl());
//
//     $request->session()->put('authenticated', true);
//     $request->session()->save();
//
//     \Illuminate\Support\Facades\Log::info('Session id: ' . $request->session()->getId());
//     \Illuminate\Support\Facades\Log::info('Saved LTI information to session: ' . ($request->session()->get('authenticated') ? 'authenticated' : 'null') . '.');
//
//     $value = encrypt($request->session()->getId());
//     $expire = config('session.expire_on_close') ? 0 : \Carbon\Carbon::now()->addMinutes(config('session.lifetime'));
//
//     setcookie($request->session()->getName(), $value, $expire, config('session.path'), config('session.domain'), config('session.secure'), config('session.http_only'));
//     header('Location: /test2');
//
//     exit();
// });
//
// Route::get('/test2', function (Request $request) {
//
//     \Illuminate\Support\Facades\Log::info('Request method: ' . $request->method());
//     \Illuminate\Support\Facades\Log::info('Request URL: ' . $request->fullUrl());
//     \Illuminate\Support\Facades\Log::info('Session id (test2): ' . $request->session()->getId());
//
//     return $request->session()->get('authenticated') ? 'authenticated!' : 'nope';
// });

Route::group(['middleware' => ['csrf']], function () {

    /**
     * Catch all route to enable HTML5 history mode in VueRouter.
     */
    Route::any('{all}', function () { return view('index'); })->where('all', '[\/\w\.-]*');
});