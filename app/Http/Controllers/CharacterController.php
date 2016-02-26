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

use Image;

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
			$character = Character::leftJoin('lineaments', 'lineaments.character_id', '=', 'characters.id')->select('characters.*', 'lineaments.note', 'lineaments.mark', 'lineaments.id as lineament_id')->where('vn_id', $vn_id)->orderBy('characters.id')->get();
			return response()->json(['data' => $character]);
		}
		else {
			$character = Character::orderBy('yobikata')->paginate(10);
			return response()->json($character);
		}


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
		$character->height = $request->input('height');
		$character->bust = $request->input('bust');
		$character->waist = $request->input('waist');
		$character->hip = $request->input('hip');
		$character->image = $request->input('image');
		$character->vndb_character_id = $request->input('vndb_character_id');
		if(Gate::denies('store-character', $character)) {
			abort(403);
		}
		$exec = $character->save();
		if($exec) {
			$url = $request->input('image');
			$local_filename = null;
			if($url) {
				$filename = basename($url);
				$local_filename = $character->id . "_" . $filename;
				// using php copy function
				// copy($url, 'reallocation/' . $filename);
				// using Intervention Image, second parameter of save method is the quality of jpg image (default to 90 if not set)
				Image::make($url)->save('reallocation/character/' . $local_filename, 100);
				// save local filename to database
				$character->local_image = $local_filename;
				$character->save();
			}

			return response()->json($character);
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

		$url = $request->input('image');
		$existing_local_filename = $character->local_image;
		$local_filename = null;
		if($url) {
			$filename = basename($url);
			$local_filename = $id . "_" . $filename;
			if($local_filename != $existing_local_filename) {
				Image::make($url)->save('reallocation/character/' . $local_filename, 100);
			}
		}

		$character->vn_id = $request->input('vn_id');
		$character->kanji = $request->input('kanji');
		$character->betsumyou = $request->input('betsumyou');
		$character->yobikata = $request->input('yobikata');
		$character->birthmonth = $request->input('birthmonth');
		$character->birthday = $request->input('birthday');
		$character->height = $request->input('height');
		$character->bust = $request->input('bust');
		$character->waist = $request->input('waist');
		$character->hip = $request->input('hip');
		$character->image = $request->input('image');
		$character->local_image = $local_filename;
		$character->vndb_character_id = $request->input('vndb_character_id');
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
