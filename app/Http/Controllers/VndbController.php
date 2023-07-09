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

	public function release2(Request $request) {
		$vndb_token = $request->input('vndb_token');
		$vndb_vn_id = $request->input('vndb_vn_id');

		$postvars = array(
			"filters" => ["vn", "=", ["id", "=", "v40520"]],
			"fields" => "id, title, alttitle, languages.title, languages.main, platforms, vns.rtype, producers.developer, producers.publisher, released, minage, patch, freeware, uncensored, official, has_ero, resolution, engine, voiced, notes, gtin, catalog, extlinks.url, extlinks.label, extlinks.name, extlinks.id, producers.name, producers.original, producers.aliases, producers.lang, producers.type, producers.description",
			"sort"=> "id",
			"results"=> 50,
			"page"=> 1,
			"count"=> true
		);

		$authorization = "Authorization: Token " . $vndb_token;

		$ch = curl_init();
		$agent = 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:10.0) Gecko/20100101 Firefox/10.0';
		$target_url = 'https://api.vndb.org/kana/release';

		curl_setopt($ch, CURLOPT_URL, $target_url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $authorization));
		curl_setopt($ch, CURLOPT_USERAGENT, $agent);
		curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'POST' );
		// curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postvars));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // response as a string
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		
		$result = curl_exec($ch);
		// print $result;

		if (curl_errno($ch)) {
			print curl_error($ch);
		}


		$result_json = json_decode($result, true);
		// $result_json = $result_json['results'];
		// return $result_json;
		// return response()->json(array());
		return response()->json(['data' => $result_json]);
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

	public function setVote2(Request $request) {
		$vndb_token = $request->input('vndb_token');
		$vndb_id = $request->input('vndb_id');
		$vote = 0 ? null : $request->input('vote') * 10;

		$postvars = array(
			"vote" => $vote
		);

		
		$ch = curl_init();
		$agent = 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:10.0) Gecko/20100101 Firefox/10.0';
		$target_url = 'https://api.vndb.org/kana/ulist/v' . $vndb_id;
		$authorization = "Authorization: Token " . $vndb_token;

		curl_setopt($ch, CURLOPT_URL, $target_url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $authorization));
		curl_setopt($ch, CURLOPT_USERAGENT, $agent);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postvars));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // response as a string
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		
		$result = curl_exec($ch);

		if (curl_errno($ch)) {
			print curl_error($ch);
		}

		return response()->json(['status' => "success"]);
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

	public function setStatus2(Request $request) {
		$vndb_token = $request->input('vndb_token');
		$vndb_id = $request->input('vndb_id');
		$vndb_status = null;
		$vndb_status = $request->input('status') == 'playing' ? 1 : $vndb_status;
		$vndb_status = $request->input('status') == 'finished' ? 2 : $vndb_status;
		$vndb_status = $request->input('status') == 'stalled' ? 3 : $vndb_status;
		$vndb_status = $request->input('status') == 'dropped' ? 4 : $vndb_status;

		$available_label_index = [1,2,3,4];
		// Remove selected index from array
		// $label_to_unset = array_diff($available_label_index, [$vndb_status]);

		$postvars = array(
			"labels_unset" => $available_label_index
		);

		$ch = curl_init();
		$agent = 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:10.0) Gecko/20100101 Firefox/10.0';
		$target_url = 'https://api.vndb.org/kana/ulist/v' . $vndb_id;
		$authorization = "Authorization: Token " . $vndb_token;

		curl_setopt($ch, CURLOPT_URL, $target_url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $authorization));
		curl_setopt($ch, CURLOPT_USERAGENT, $agent);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postvars));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // response as a string
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		
		$result = curl_exec($ch);

		// Separate curl to set status
		$postvars = array(
			"labels_set" => [$vndb_status]
		);

		$ch = curl_init();
		$agent = 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:10.0) Gecko/20100101 Firefox/10.0';
		$target_url = 'https://api.vndb.org/kana/ulist/v' . $vndb_id;
		$authorization = "Authorization: Token " . $vndb_token;

		curl_setopt($ch, CURLOPT_URL, $target_url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $authorization));
		curl_setopt($ch, CURLOPT_USERAGENT, $agent);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postvars));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // response as a string
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		
		$result = curl_exec($ch);

		if (curl_errno($ch)) {
			print curl_error($ch);
		}

		return response()->json(['status' => "success"]);
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
