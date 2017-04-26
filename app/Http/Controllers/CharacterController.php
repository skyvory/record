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
use App\CharacterHistory;
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
		if(Gate::denies('read-character')) {
			abort(403);
		};

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

			$data = array();
			foreach ($character as $chara) {
				$chara['local_url'] = url('/reallocation/character') .'/' . $chara['local_image'];
				$data[] = $chara;
			}

			return response()->json(['data' => $data]);
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
		if(Gate::denies('create-character')) {
			abort(403);
		};

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
		if(Gate::denies('read-character')) {
			abort(403);
		};

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
		if(Gate::denies('update-character')) {
			abort(403);
		};

		$character = Character::find($id);

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
		
		// Write history if any property change detected
		if($character->isDirty()) {
			if(!$this->writeHistory($character->id)) {
				return response()->json(['status' => 'error', 'errors' => ['someting is wrong with history logging']]);
			}
		}
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
		if(Gate::denies('delete-character')) {
			abort(403);
		};

		$character = Character::find($id);
		$character->record_status = 3;

		if(!$this->writeHistory($character->id)) {
			return response()->json(['status' => 'error', 'errors' => ['someting is wrong with history logging']]);
		}

		$exec = $character->save();
		if($exec) {
			return response()->json(['status' => 'success']);
		}
	}

	private function writeHistory($id)
	{
		\DB::beginTransaction();
		$character = Character::find($id);

		$history = new CharacterHistory();
		$history->character_id = $character->id;
		$max_revision_sequence = CharacterHistory::select('revision_sequence')->where('character_id', $character->id)->lockForUpdate()->max('revision_sequence');
		$history->revision_sequence = $max_revision_sequence ? $max_revision_sequence + 1 : 1;
		$history->modified_date = $character->updated_at;
		$history->vn_id = $character->vn_id;
		$history->name_original = $character->name_original;
		$history->name_betsumyou = $character->name_betsumyou;
		$history->name_furigana = $character->name_furigana;
		$history->birthmonth = $character->birthmonth;
		$history->birthday = $character->birthday;
		$history->age = $character->age;
		$history->height = $character->height;
		$history->weight = $character->weight;
		$history->bust = $character->bust;
		$history->waist = $character->waist;
		$history->hip = $character->hip;
		$history->blood_type = $character->blood_type;
		$history->image = $character->image;
		$history->local_image = $character->local_image;
		$history->description = $character->description;
		$history->vndb_character_id = $character->vndb_character_id;
		$history->record_status = $character->record_status;

		$exec_history = $history->save();
		\DB::commit();
		return $exec_history;
	}

	public function storeImage(Request $request){
		if(Gate::denies('store-character-image')) {
			abort(403);
		};

		$this->validate($request, [
			'character_id' => 'required|integer|min:1|exists:characters,id',
			'image' => 'required|file|min:1'
		]);

		$character_id = $request->input('character_id');

		if($request->hasFile('image') && $request->file('image')->isvalid()) {
			$file = $request->file('image');
			$original_filename = $file->getClientOriginalName();

			if(PHP_OS !== "WINNT") {
				$file->storeAs('character', $character_id . '_' . $original_filename);
			}

			$hashed_filename = $file->hashName();
			$local_filename = $character_id . '_' . $hashed_filename;

			// Rename .jpeg to .jpg
			if($file->extension() == 'jpeg') {
				$local_filename = substr_replace($local_filename, '', -2, 1);
			}

			// Check if image already exist
			if(file_exists(public_path() . '/reallocation/screenshot/' . $local_filename)) {
				
				// Check if database has the record and write if not
				$character = Character::find($character_id);
				if($character->local_image !== $local_filename) {
					$character->local_image = $local_filename;
					$character->save();
				}
				return response()->json(['data' => $character]);
			}

			if(PHP_OS === "WINNT") {
				$exec_save = $file->move(public_path('\reallocation\character'), $local_filename);
			}
			else {
				$exec_save = $file->move(public_path('/reallocation/character'), $local_filename);
			}

			// Write record to database
			$character = Character::find($character_id);
			$character->local_image = $local_filename;
			$exec = $character->save();

			// Include local URL to returned response
			$character['local_url'] = url('/reallocation/character') . '/' . $local_filename;

			if($exec)
				return response()->json(['data' => $character]);
		}
	}
}
