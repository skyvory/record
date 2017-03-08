<?php

// appended header to forcefully enable cors
header('Access-Control-Allow-Origin:  *');
header('Access-Control-Allow-Methods:  POST, GET, OPTIONS, PUT, DELETE');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Origin, Authorization');
// end of forecefull header append

use Illuminate\Http\Request;

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

// Route::group(['prefix' => 'api'], function() {
	// user authentication mean
	Route::resource('authenticate', 'AuthenticateController', ['only' => ['index']]);
	Route::post('authenticate', 'AuthenticateController@authenticate');
	// return authenticated user details
	Route::get('who', 'AuthenticateController@who');
	// resource utilize default handle of action provided by resource controller
	// resource routing
	Route::resource('user', 'UserController', ['except' => ['create', 'edit']]);
	Route::resource('vn', 'VnController', ['except' => ['index', 'show', 'store', 'edit']]);
	Route::group(['prefix' => 'vn'], function() {
		Route::get('/', 'VnController@getVns');
		Route::post('/', 'VnController@create');
		Route::get('/{id}', 'VnController@getVn');
		Route::post('/removeRelation', 'VnController@removeRelation');
		Route::post('/refreshCover/{id}', 'VnController@refreshCover');
	});
	Route::resource('assessment', 'AssessmentController', ['except' => ['index', 'show', 'create', 'edit']]);
	Route::group(['prefix' => 'assessment'], function() {
		Route::get('/', 'AssessmentController@getAssessments');
		Route::get('/{id}', 'AssessmentController@getOneAssessment');
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
// });