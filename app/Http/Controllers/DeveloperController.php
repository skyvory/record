<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use JWTAuth;
use Tymon\JWTAuth\Exception\JWTException;

use Illuminate\Http\Response;
use Gate;
use App\Developer;

class DeveloperController extends Controller
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
	// developer search parameter find only exact string
	public function getDevelopers(Request $request)
	{
		if(Gate::denies('read-developer')) {
			abort(403);
		};

		if($request->input('original')) {
			$developer = Developer::where('original', $request->input('original'))->first();
			return response()->json($developer);
		}
		// $developer = Developer::orderBy('name_en')->paginate(1000);
		// return response()->json($developer);
		$developer = Developer::orderBy('original')->where('record_status', 1)->get();
		return response()->json(['data' => $developer]);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function create(Request $request)
	{
		if(Gate::denies('create-developer')) {
			abort(403);
		};

		$this->validate($request, [
			'original' => 'required|min:1'
		]);

		$developer = new Developer();
		$developer->original = $request->input('original');
		$developer->furi = $request->input('furi');
		$developer->romaji = $request->input('romaji');
		$developer->record_status = 1;
		$exec = $developer->save();
		if($exec) {
			return response()->json($developer);
		}
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function getDeveloper($id)
	{
		if(Gate::denies('read-developer')) {
			abort(403);
		};

		$developer = Developer::where('id', $id)->whereIn('record_status', array(1,2))->first();
		if($developer) {
			return response()->json($developer);
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
		if(Gate::denies('update-developer')) {
			abort(403);
		};

		$developer = Developer::find($id);
		$developer->original = $request->input('original');
		$developer->furi = $request->input('furi');
		$developer->romaji = $request->input('romaji');
		$exec = $developer->save();
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
		if(Gate::denies('delete-developer')) {
			abort(403);
		};

		$developer = developer::find($id);
		$developer->record_status = 3;
		$exec = $developer->save();
		if($exec) {
			return response()->json(['status' => 'success']);
		}
	}

}
