<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
		return view('welcome');
});

Route::group(['prefix' => 'api'], function() {
	// user authentication mean
	Route::resource('authenticate', 'AuthenticateController', ['only' => ['index']]);
	Route::post('authenticate', 'AuthenticateController@authenticate');
	// return authenticated user details
	Route::get('who', 'AuthenticateController@who');
	// resource utilize default handle of action provided by resource controller
	Route::resource('vn', 'VnController', ['except' => ['create', 'edit']]);
	Route::resource('user', 'UserController', ['except' => ['create', 'edit']]);
});

