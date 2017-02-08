<?php

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index');

Route::group(['middleware' => 'auth'], function () {
    Route::post('/user/change-password', function (\Illuminate\Http\Request $request) {
        $user = Auth::user();
        $user->password = bcrypt($request->password);
        $user->save();
    });
    Route::get('/filter/fetch', 'FilterController@fetch');
    Route::post('/filter/update', 'FilterController@update');
    Route::post('/filter/save', 'FilterController@save');

    Route::get('/sites', 'SiteController@index');
    Route::get('/sites/fetch', 'SiteController@fetch');
    Route::post('/sites/save', 'SiteController@save');
    Route::post('/sites/delete', 'SiteController@delete');

    Route::get('/products', 'ProductController@index');

    Route::post('/notifier/save', 'NotifierController@save');
    Route::get('/notifier', 'NotifierController@index');

    Route::get('/log', 'LogController@index');
    Route::get('/logs/{filename}', function ($file) {
        $file = '../logs/' . $file;
        return response()->download($file);
    });
});
