<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

use Illuminate\Http\Response;
use Gate;
use App\User;
use App\Note;

class NoteController extends Controller
{
	public function __construct() {
		$this->middleware('jwt.auth', ['except' => ['authenticate']]);
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$user = JWTAuth::parseToken()->authenticate();
		$note = Note::where('user_id', $user->id)->orderBy('date_start', 'desc')->paginate(10);
		if(Gate::denies('index-note', $note)) {
			abort(403);
		}
		return response()->json($note);
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
		$note = new Note();
		$note->vn_id = $request->input('vn_id');
		$note->user_id = $request->user()->id;
		$note->interface = $request->input('interface');
		$note->gene = $request->input('gene');
		$note->setting = $request->input('setting');
		$note->side_chara = $request->input('side_chara');
		$note->story = $request->input('story');
		$note->route = $request->input('route');
		$note->bgm = $request->input('bgm');
		$note->terminology = $request->input('terminology');
		$note->timescape = $request->input('timescape');
		$note->quote = $request->input('quote');
		$note->other = $request->input('other');
		if(Gate::denies('store-note', $note)) {
			abort(403);
		}
		$exec = $note->save();
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
		$note = Note::where('user_id', $user->id)->find($id);
		if(Gate::denies('show-note', $note)) {
			abort(403);
		}
		if($note) {
			return response()->json($note);
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
		$note = Note::find($id);
		if(Gate::denies('update-note', $note)) {
			abort(403);
		}
		$note->interface = $request->input('interface');
		$note->gene = $request->input('gene');
		$note->setting = $request->input('setting');
		$note->side_chara = $request->input('side_chara');
		$note->story = $request->input('story');
		$note->route = $request->input('route');
		$note->bgm = $request->input('bgm');
		$note->terminology = $request->input('terminology');
		$note->timescape = $request->input('timescape');
		$note->quote = $request->input('quote');
		$note->other = $request->input('other');
		$exec = $note->save();
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
		$note = Note::find($id);
		if(Gate::denies('delete-note', $note)) {
			abort(403);
		}
		$exec = $note->delete();
		if($exec) {
			return response()->json(["status" => "success"]);
		}
	}
}
