<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Response;
use Twitter;
use Session;
// use Redirect;
use Illuminate\Support\Facades\Input;
use App\OnlineService;

class TweetController extends Controller
{
	public function __construct() {
		$this->middleware('jwt.auth', ['except' => ['authenticate']]);
	}

	public function postStatus(Request $request) {
		$user = JWTAuth::parseToken()->authenticate();
		if(!$user) {
			return response()->json(['status' => 'error']);
		}

		if(!Session::get('access_token')) {
			$credential = OnlineService::where('user_id', $user->id)->first();

			if(!$credential) {
				return response()->json(['status' => 'error']);
			}

			$token = [
				'oauth_token' => $credential->twitter_oauth_token,
				'oauth_token_secret' => $credential->twitter_oauth_token_secret,
				'user_id' => $credential->twitter_user_id,
				'screen_name' => $credential->twitter_screen_name,
				'x_auth_expires' => $credential->twitter_x_auth_expires
			];
			Session::put('access_token', $token);
		}

		$status = $request->input('status');
		$exec = Twitter::postTweet(['status' => $status, 'format' => 'json']);
		return $exec;
	}
	public function check() {
		//
	}
}
