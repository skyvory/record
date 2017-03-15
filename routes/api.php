<?php

// appended header to forcefully enable cors
header('Access-Control-Allow-Origin:  *');
header('Access-Control-Allow-Methods:  POST, GET, OPTIONS, PUT, DELETE');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Origin, Authorization, X-Requested-With, Accept');
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

	Route::group(['prefix' => 'vn'], function() {
		Route::get('/', 'VnController@getVns');
		Route::post('/', 'VnController@create');
		Route::get('/{id}', 'VnController@getVn');
		Route::post('/removeRelation', 'VnController@removeRelation');
		Route::post('/refreshCover/{id}', 'VnController@refreshCover');
		Route::put('/{id}', 'VnController@update');
		Route::delete('/{id}', 'VnController@delete');
		Route::post('/screenshot', 'VnController@storeScreenshot');
		Route::put('/screenshot', 'VnController@storeScreenshot');
		Route::get('/screenshots/{id}', 'VnController@getScreenshots');
		Route::delete('/screenshot/{id}', 'VnController@removeScreenshot');
		Route::put('/screenshot/{id}', 'VnController@updateScreenshotProperty');
	});

	Route::group(['prefix' => 'assessment'], function() {
		Route::get('/', 'AssessmentController@getAssessments');
		Route::get('/{id}', 'AssessmentController@getOneAssessment');
		Route::post('/', 'AssessmentController@create');
		Route::put('/{id}', 'AssessmentController@update');
		Route::delete('/{id}', 'AssessmentController@delete');
	});

	Route::group(['prefix' => 'character'], function() {
		Route::get('/', 'CharacterController@getCharacters');
		Route::post('/', 'CharacterController@create');
		Route::get('/{id}', 'CharacterController@getCharacter');
		Route::put('/{id}', 'CharacterController@update');
		Route::delete('/{id}', 'CharacterController@delete');
	});

	Route::group(['prefix' => 'developer'], function() {
		Route::post('/', 'DeveloperController@create');
		Route::get('/', 'DeveloperController@getDevelopers');
		Route::get('/{id}', 'DeveloperController@getDeveloper');
		Route::put('/{id}', 'DeveloperController@update');
		Route::delete('/{id}', 'DeveloperController@delete');
	});

	Route::group(['prefix' => 'lineament'], function() {
		Route::get('/', 'LineamentController@getLineaments');
		Route::post('/', 'LineamentController@create');
		Route::get('/{id}', 'LineamentController@getLineament');
		Route::put('/{id}', 'LineamentController@update');
		Route::delete('/{id}', 'LineamentController@delete');
	});

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