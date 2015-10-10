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
	Route::resource('authenticate', 'AuthenticateController', ['only' => ['index']]);
	Route::post('authenticate', 'AuthenticateController@authenticate');
	Route::get('who', 'AuthenticateController@who');
	Route::group(['prefix' => 'vn'], function() {
		Route::post('list', 'VnController@index');
		Route::post('{id}', 'VnController@show')->where('id', '[0-9]+');
		Route::post('{id}/edit', 'VnController@edit');
	});
});