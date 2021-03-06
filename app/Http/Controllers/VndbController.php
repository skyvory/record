<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Response;

use App\VndbClient\Client;

class VndbController extends Controller
{
	public function dbstat(Request $request) {
		$client = new Client();
		$client->connect();
		$client->login($username = $request->input('username'), $password = $request->input('password'));
		$res = $client->sendCommand('dbstats');
		$res_after = json_decode(json_encode($res), true);
		return response()->json($res_after);
	}

	public function vn(Request $request) {
		$vndb_username_hash = $request->input('vndb_username_hash');
		$vndb_password_hash = $request->input('vndb_password_hash');
		$auth = app('App\Http\Controllers\SettingController')->retrieveVndbAuth($vndb_username_hash, $vndb_password_hash);

		$client = new Client();
		$client->connect();
		$client->login($username = $auth['username'], $password = $auth['password']);
		$res = $client->sendCommand('get vn basic,details,anime,relations,tags,stats (id = ' . (int)$request->input('vndb_id') . ')');
		$res_after = json_decode(json_encode($res), true);
		return response()->json($res_after);
	}
	public function release(Request $request) {
		$vndb_username_hash = $request->input('vndb_username_hash');
		$vndb_password_hash = $request->input('vndb_password_hash');
		$auth = app('App\Http\Controllers\SettingController')->retrieveVndbAuth($vndb_username_hash, $vndb_password_hash);

		$client = new Client();
		$client->connect();
		$client->login($username = $auth['username'], $password = $auth['password']);
		$res = $client->sendCommand('get release basic,details,vn,producers (vn = ' . (int)$request->input('vndb_id') . ')');
		$res_after = json_decode(json_encode($res), true);
		return response()->json($res_after);
	}
	public function character(Request $request) {
		$vndb_username_hash = $request->input('vndb_username_hash');
		$vndb_password_hash = $request->input('vndb_password_hash');
		$auth = app('App\Http\Controllers\SettingController')->retrieveVndbAuth($vndb_username_hash, $vndb_password_hash);

		$client = new Client();
		$client->connect();
		$client->login($username = $auth['username'], $password = $auth['password']);
		$page = $request->input('page') ?: 1;
		$options = json_encode(array('results' => 25, 'page' => $page));
		$res = $client->sendCommand('get character basic,details,meas,traits (vn = ' . (int)$request->input('vndb_id') . ') ' . $options);
		$res_after = json_decode(json_encode($res), true);
		return response()->json($res_after);
	}
	public function setVote(Request $request) {
		$vndb_username_hash = $request->input('vndb_username_hash');
		$vndb_password_hash = $request->input('vndb_password_hash');
		$auth = app('App\Http\Controllers\SettingController')->retrieveVndbAuth($vndb_username_hash, $vndb_password_hash);

		$client = new Client();
		$client->connect();
		$client->login($username = $auth['username'], $password = $auth['password']);
		$vndb_id = $request->input('vndb_id');
		$vote = $request->input('vote') * 10;
		$res = $client->sendCommand('set votelist ' . (int)$vndb_id . ' {"vote": ' . (int)$vote . '}');
		$res_after = json_decode(json_encode($res), true);
		return response()->json($res_after);
	}
	public function setStatus(Request $request) {
		$vndb_username_hash = $request->input('vndb_username_hash');
		$vndb_password_hash = $request->input('vndb_password_hash');
		$auth = app('App\Http\Controllers\SettingController')->retrieveVndbAuth($vndb_username_hash, $vndb_password_hash);
		
		$client = new Client();
		$client->connect();
		$client->login($username = $auth['username'], $password = $auth['password']);
		$vndb_id = $request->input('vndb_id');
		$vndb_status = null;
		$vndb_status = $request->input('status') == 'finished' ? 2 : $vndb_status;
		$vndb_status = $request->input('status') == 'stalled' ? 3 : $vndb_status;
		$vndb_status = $request->input('status') == 'dropped' ? 4 : $vndb_status;
		$res = $client->sendCommand('set vnlist ' . (int)$vndb_id . ' {"status": ' . (int)$vndb_status . '}');
		$res_after = json_decode(json_encode($res), true);
		return response()->json($res_after);
	}

	
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		//
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		//
	}
}
