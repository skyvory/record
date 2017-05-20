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

Route::get('/twitterauth/login', 'TwitterAuthController@login');
Route::get('/twitterauth/callback', [
	'as' => 'twitterauth.callback',
	'uses' => 'TwitterAuthController@callback'
	]);
Route::get('/twitterauth/error', 'TwitterAuthController@error');
Route::get('/twitterauth/logout', 'TwitterAuthController@logout');

// Route::get('/twitter/tweet/{tweet}', 'TweetController@tweet');
// Route::get('/twitter/check', [
// 	'as' => 'twitter.check',
// 	'uses' => 'TweetController@check'
// 	]);