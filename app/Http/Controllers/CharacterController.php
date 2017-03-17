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
use App\Http\Controllers\ExtensionPlus;

class CharacterController extends Controller
{

	use ExtensionPlus;

	public function __construct() {
		$this->middleware('jwt.auth', ['except' => ['authenticate']]);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function getCharacters(Request $request)
	{
		$vn_id = $request->input('vn_id');
		$user = JWTAuth::parseToken()->authenticate();

		if(!empty($vn_id)) {
			$character = Character::leftJoin('lineaments', function($join) use ($user)
			{
				$join->on('lineaments.character_id', '=', 'characters.id');
				$join->on('lineaments.user_id', '=', \DB::raw($user->id));
			})
			->select('characters.*', 'lineaments.note', 'lineaments.mark', 'lineaments.id as lineament_id')
			->where('vn_id', $vn_id)
			->where('record_status', 1)
			->orderBy('characters.id')
			->get();
			return response()->json(['data' => $character]);
		}
		else {
			$character = Character::orderBy('name_furigana')->paginate(10);
			return response()->json($character);
		}
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function create(Request $request)
	{
		$character = new Character();
		$character->vn_id = $request->input('vn_id');
		$character->name_original = $request->input('name_original');
		$character->name_betsumyou = $request->input('name_betsumyou');
		$character->name_furigana = $request->input('name_furigana');
		$character->birthmonth = !empty(trim($request->input('birthmonth'))) ? $request->input('birthmonth') : null;
		$character->birthday = !empty(trim($request->input('birthday'))) ? $request->input('birthday') : null;
		$character->age = !empty(trim($request->input('age'))) ? $request->input('age') : null;
		$character->height = !empty(trim($request->input('height'))) ? $request->input('height') : null;
		$character->weight = !empty(trim($request->input('weight'))) ? $request->input('weight') : null;
		$character->bust = !empty(trim($request->input('bust'))) ? $request->input('bust') : null;
		$character->waist = !empty(trim($request->input('waist'))) ? $request->input('waist') : null;
		$character->hip = !empty(trim($request->input('hip'))) ? $request->input('hip') : null;
		$character->blood_type = !empty(trim($request->input('blood_type'))) ? $request->input('blood_type') : null;
		$character->image = $request->input('image');
		$character->description = !empty(trim($request->input('description'))) ? $request->input('description') : null;
		$character->vndb_character_id = $request->input('vndb_character_id');
		$character->record_status = 1;
		if(Gate::denies('store-character', $character)) {
			abort(403);
		}
		$exec = $character->save();
		if($exec) {
			$url = $request->input('image');
			// $url = 'https://www.ancestry.com/wiki/images/archive/a/a9/20100708215937!Example.jpg';
			$local_filename = null;
			if($url) {
				$filename = basename($url);
				$local_filename = $character->id . "_" . $filename;
				$imgpath = 'reallocation/character/' . $local_filename;
				
				// using php copy function
				// copy($url, 'reallocation/' . $filename);
				// using Intervention Image, second parameter of save method is the quality of jpg image (default to 90 if not set)
				// Image::make($url)->save('reallocation/character/' . $local_filename, 100);

				if($this->saveRemoteImage($url, $imgpath)) {
					// save local filename to database
					$character->local_image = $local_filename;
					$character->save();
				}
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
	public function getCharacter($id)
	{
		// $character = JWTAuth::parseToken()->authenticate();
		$character = Character::where('id', $id)->whereIn('record_status', array(1,2))->first();
		if($character) {
			return response()->json($character);
		}
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
				 $this->saveRemoteImage($url, 'reallocation/character/' . $local_filename);
				// Image::make($url)->save('reallocation/character/' . $local_filename, 100);
			}
		}

		$character->vn_id = $request->input('vn_id');
		$character->name_original = $request->input('name_original');
		$character->name_betsumyou = $request->input('name_betsumyou');
		$character->name_furigana = $request->input('name_furigana');
		$character->birthmonth = !empty(trim($request->input('birthmonth'))) ? $request->input('birthmonth') : null;
		$character->birthday = !empty(trim($request->input('birthday'))) ? $request->input('birthday') : null;
		$character->age = !empty(trim($request->input('age'))) ? $request->input('age') : null;
		$character->height = !empty(trim($request->input('height'))) ? $request->input('height') : null;
		$character->weight = !empty(trim($request->input('weight'))) ? $request->input('weight') : null;
		$character->bust = !empty(trim($request->input('bust'))) ? $request->input('bust') : null;
		$character->waist = !empty(trim($request->input('waist'))) ? $request->input('waist') : null;
		$character->hip = !empty(trim($request->input('hip'))) ? $request->input('hip') : null;
		$character->blood_type = !empty(trim($request->input('blood_type'))) ? $request->input('blood_type') : null;
		$character->image = $request->input('image');
		$character->local_image = $local_filename;
		$character->description = !empty(trim($request->input('description'))) ? $request->input('description') : null;
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
	public function delete($id)
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
