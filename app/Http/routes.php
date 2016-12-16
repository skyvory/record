<?php

// appended header to forcefully enable cors
header('Access-Control-Allow-Origin:  *');
header('Access-Control-Allow-Methods:  POST, GET, OPTIONS, PUT, DELETE');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Origin, Authorization');
// end of forecefull header append

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
	Route::resource('assessment', 'AssessmentController', ['except' => ['index', 'create', 'edit']]);
	Route::group(['prefix' => 'assessment'], function() {
		Route::get('/', 'AssessmentController@getAssessments');
	});
	Route::resource('character', 'CharacterController', ['except' => ['index', 'create', 'edit']]);
	Route::group(['prefix' => 'character'], function() {
		Route::get('/', 'CharacterController@getCharacters');
	});
	Route::resource('developer', 'DeveloperController', ['except' => ['create', 'edit']]);
	Route::resource('lineament', 'LineamentController', ['except' => ['create', 'edit']]);
	Route::resource('note', 'NoteController', ['except' => ['create', 'edit']]);
	Route::resource('stock', 'StockController', ['except' => ['create', 'edit']]);

	Route::group(['prefix' => 'vndb'], function() {
		Route::post('/dbstat', 'VndbController@dbstat');
		Route::post('/vn', 'VndbController@vn');
		Route::post('/release', 'VndbController@release');
		Route::post('/character', 'VndbController@character');
		Route::post('/setVote', 'VndbController@setVote');
		Route::post('/setStatus', 'VndbController@setStatus');
	});
});