<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use JWTAuth;
use Tymon\JWTAuth\Exception\JWTException;

use Illuminate\Http\Response;
use Gate;
use App\User;
use App\Character;

class CharacterController extends Controller
{

	public function __construct() {
		$this->middleware('jwt.auth', ['except' => ['authenticate']]);
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{
		// $user = JWTAuth::parseToken()->authenticate();
		$vn_id = $request->input('vn_id');
		if($vn_id != null) {
			$character = Character::where('vn_id', $vn_id)->orderBy('id')->get();
		}
		else {
			$character = Character::orderBy('yobikata')->paginate(10)->get();
		}

		// $character = json_decode($character);
		// $chara = $character->toArray();
		// $chara = json_encode($character);
		// var_dump($character);
		return response()->json(['data' => $character]);

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
		$character = new Character();
		$character->vn_id = $request->input('vn_id');
		$character->kanji = $request->input('kanji');
		$character->betsumyou = $request->input('betsumyou');
		$character->yobikata = $request->input('yobikata');
		$character->birthmonth = $request->input('birthmonth');
		$character->birthday = $request->input('birthday');
		if(Gate::denies('store-character', $character)) {
			abort(403);
		}
		$exec = $character->save();
		if($exec) {
			return response()->json(['status' => 'success']);
		}
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		// $character = JWTAuth::parseToken()->authenticate();
		$character = Character::find($id);
		if($character) {
			return response()->json($character);
		}
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
		$character = Character::find($id);
		if(Gate::denies('update-character', $character)) {
			abort(403);
		}
		$character->vn_id = $request->input('vn_id');
		$character->kanji = $request->input('kanji');
		$character->betsumyou = $request->input('betsumyou');
		$character->yobikata = $request->input('yobikata');
		$character->birthmonth = $request->input('birthmonth');
		$character->birthday = $request->input('birthday');
		$exec = $character->save();
		if($exec) {
			return response()->json(['status' => 'success']);
		}
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		$character = Character::find($id);
		if(Gate::denies('delete-character', $character)) {
			abort(403);
		}
		$exec = $character->delete();
		if($exec) {
			return response()->json(['status' => 'success']);
		}
	}
}
