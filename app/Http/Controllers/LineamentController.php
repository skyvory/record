<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

use Illuminate\Http\Response;
use Gate;
use App\Lineament;

class LineamentController extends Controller
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
		$user = JWTAuth::parseToken()->authenticate();
		$vn_id = $request->input('vn_id');
		if($vn_id != null) {
			$lineament = Lineament::leftJoin('characters', 'characters.id', '=', 'lineaments.character_id')->select('characters.*', 'lineaments.id as lineament_id', 'lineaments.character_id', 'lineaments.note', 'lineaments.mark')->where('user_id', $user->id)->orderBy('characters.created_at')->get();
			if(Gate::denies('index-lineament', $lineament)) {
				abort(403);
			}
			return response()->json(['data' => $lineament]);
		}
		else {
			$lineament = Lineament::where('user_id', $user->id)->orderBy('created_at')->paginate(10);
			if(Gate::denies('index-lineament', $lineament)) {
				abort(403);
			}
			$liineament->note = htmlentities($lineament->note);
			return response()->json($lineament);
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
		$check = Lineament::where('user_id', $request->user()->id)->where('character_id', $request->input('character_id'))->first();
		if($check) {
			return response()->json(['status' => 'lineament is already exist, use update method to update']);
		}
		$lineament = new Lineament();
		$lineament->user_id = $request->user()->id;
		$lineament->character_id = $request->input('character_id');
		$lineament->note = $request->input('note');
		$lineament->mark = $request->input('mark');
		if(Gate::denies('store-lineament', $lineament)) {
			abort(403);
		}
		$exec = $lineament->save();
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
		$user = JWTAuth::parseToken()->authenticate();
		$lineament = Lineament::where('user_id', $user->id)->find($id);
		if(Gate::denies('show-lineament', $lineament)) {
			abort(403);
		}
		if($lineament) {
			return response()->json($lineament);
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
		// $user = JWTAuth::parseToken()->authenticate();
		$lineament = Lineament::where('user_id', $request->user()->id)->where('character_id', $request->input('character_id'))->first();
		if($lineament->id != $id) {
			return response()->json(['status' => 'Database fatal conflict! Multiple lineaments on a character detected']);
		}
		if(Gate::denies('update-lineament', $lineament)) {
			abort(403);
		}
		$lineament->note = $this->decodeInput($request->input('note'));
		$lineament->mark = $request->input('mark');
		$exec = $lineament->save();
		if($exec) {
			return response()->json(["status" => "success"]);
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
		$lineament = Lineament::find($id);
		if(Gate::denies('delete-lineament', $lineament)) {
			abort(403);
		}
		$exec = $lineament->delete();
		if($exec) {
			return response()->json(["status" => "success"]);
		}
	}

	private function decodeInput($html) {
		$html = html_entity_decode($html);
		$html = preg_replace('#<br\s*/?>#i', "\n", $html);
		return $html;
	}
}
