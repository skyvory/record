<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Response;
use Twitter;
use Session;
use Redirect;
use Illuminate\Support\Facades\Input;
use App\OnlineService;

class TwitterAuthController extends Controller
{
	public function __construct() {
	}

    	public function login()
    	{
    		$user = JWTAuth::parseToken()->authenticate();
    		Session::put('user_id', $user->id);
	   	// your SIGN IN WITH TWITTER  button should point to this route
   		$sign_in_twitter = true;
   		$force_login = false;

   		// Make sure we make this request w/o tokens, overwrite the default values in case of login.
   		Twitter::reconfig(['token' => '', 'secret' => '']);
   		$token = Twitter::getRequestToken(route('twitterauth.callback'));

   		if (isset($token['oauth_token_secret']))
   		{
   			$url = Twitter::getAuthorizeURL($token, $sign_in_twitter, $force_login);

   			Session::put('oauth_state', 'start');
   			Session::put('oauth_request_token', $token['oauth_token']);
   			Session::put('oauth_request_token_secret', $token['oauth_token_secret']);

   			return Redirect::to($url);
   		}

   		return Redirect::route('twitterauth.error');
	}

	public function callback() {
		// You should set this route on your Twitter Application settings as the callback
			// https://apps.twitter.com/app/YOUR-APP-ID/settings
		if (Session::has('oauth_request_token'))
		{
			$request_token = [
				'token'  => Session::get('oauth_request_token'),
				'secret' => Session::get('oauth_request_token_secret'),
			];

			Twitter::reconfig($request_token);

			$oauth_verifier = false;

			if (Input::has('oauth_verifier'))
			{
				$oauth_verifier = Input::get('oauth_verifier');
				// getAccessToken() will reset the token for you
				$token = Twitter::getAccessToken($oauth_verifier);
			}

			if (!isset($token['oauth_token_secret']))
			{
				return Redirect::route('twitterauth.error')->with('flash_error', 'We could not log you in on Twitter.');
			}

			$credentials = Twitter::getCredentials();

			if (is_object($credentials) && !isset($credentials->error))
			{
				// $credentials contains the Twitter user object with all the info about the user.
				// Add here your own user logic, store profiles, create new users on your tables...you name it!
				// Typically you'll want to store at least, user id, name and access tokens
				// if you want to be able to call the API on behalf of your users.

				// This is also the moment to log in your users if you're using Laravel's Auth class
				// Auth::login($user) should do the trick.

				Session::put('access_token', $token);

				$user_id = Session::get('user_id');
				$credential = OnlineService::firstOrNew(['user_id' => $user_id]);
				$credential->twitter_oauth_token = $token['oauth_token'];
				$credential->twitter_oauth_token_secret = $token['oauth_token_secret'];
				$credential->twitter_user_id = $token['user_id'];
				$credential->twitter_screen_name = $token['screen_name'];
				$credential->twitter_x_auth_expires = $token['x_auth_expires'];

				if($credential->save()) {
					// $stat = Twitter::postTweet(['status' => 'init2', 'format' => 'json']);
					// print_r($stat);
					// echo "<br>";
					// echo "<br>";
					print_r(Session::all());
					return;
					return view('twitter');
				}

				echo "Unknown error! Please contact the administrator.";
				return;

				// return Redirect::to('/')->with('flash_notice', 'Congrats! You\'ve successfully signed in!');
			}

			return Redirect::route('twitterauth.error')->with('flash_error', 'Crab! Something went wrong while signing you up!');
		}
	}

	public function error() {
		//
	}
	public function logout() {
		Session::forget('access_token');
		return Redirect::to('/')->with('flash_notice', 'You\'ve successfully logged out!');
	}
}
