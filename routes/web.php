<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route::get('/lti/launch', 'LtiController@index');
Route::post('/lti/launch', 'LtiController@index');

// Route::get('/test', function (Request $request) {
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
//     \Illuminate\Support\Facades\Log::info('Request method: ' . $request->method());
//     \Illuminate\Support\Facades\Log::info('Request URL: ' . $request->fullUrl());
//     \Illuminate\Support\Facades\Log::info('Session id (test2): ' . $request->session()->getId());
//
//     return $request->session()->get('authenticated') ? 'authenticated!' : 'nope';
// });

Route::get('/js/lang.js', function () {
    //$strings = \Illuminate\Support\Facades\Cache::rememberForever('lang.js', function () {

    $lang = config('app.locale');
    $fallback = config('app.fallback_locale');

    $files = glob(resource_path('lang/' . $lang . '/*.php'));
    $strings = [];

    foreach ($files as $file) {
        $name = basename($file, '.php');
        $strings[$name] = require $file;
    }

    $fallbackFiles = glob(resource_path('lang/' . $fallback . '/*.php'));
    $fallbackStrings = [];

    foreach ($fallbackFiles as $file) {
        $name = basename($file, '.php');
        $fallbackStrings[$name] = require $file;
    }

    $strings = array_replace_recursive($fallbackStrings, $strings);

    //return $strings;
    //});

    header('Content-Type: text/javascript');
    echo('window.i18n = ' . json_encode($strings) . ';');

    exit();
});

Route::group(['middleware' => ['csrf']], function () {
    /**
     * Catch all route to enable HTML5 history mode in VueRouter.
     */
    Route::any('{all}', function () {
        return view('index');
    })->where('all', '[\/\w\.-]*');
});
