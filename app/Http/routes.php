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
	// resource routing
	Route::resource('user', 'UserController', ['except' => ['create', 'edit']]);
	Route::resource('vn', 'VnController', ['except' => ['create', 'edit']]);
	Route::resource('assessment', 'AssessmentController', ['except' => ['create', 'edit']]);
	Route::resource('character', 'CharacterController', ['except' => ['create', 'edit']]);
	Route::resource('developer', 'DeveloperController', ['except' => ['create', 'edit']]);
	Route::resource('lineament', 'LineamentController', ['except' => ['create', 'edit']]);
	Route::resource('note', 'NoteController', ['except' => ['create', 'edit']]);
	Route::resource('stock', 'StockController', ['except' => ['create', 'edit']]);
});

Route::group(['prefix' => 'vndb'], function() {
	Route::post('/dbstat', 'VndbController@dbstat');
	Route::post('/vn', 'VndbController@vn');
	Route::post('/release', 'VndbController@release');
	Route::post('/character', 'VndbController@character');
	Route::post('/setVote', 'VndbController@setVote');
	Route::post('/setStatus', 'VndbController@setStatus');
});
