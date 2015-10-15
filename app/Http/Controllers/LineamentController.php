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
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$user = JWTAuth::parseToken()->authenticate();
		$lineament = Lineament::where('user_id', $user->id)->orderBy('date_created', 'desc')->paginate(10);
		if(Gate::denies('index-lineament', $lineament)) {
			abort(403);
		}
		return response()->json($lineament);
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
		$lineament = new Lineament();
		$lineament->user_id = $request->user()->id;
		$lineament->character_id = $request->post('character_id');
		$lineament->note = $request->post('note');
		$lineament->mark = $request->post('mark');
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
		$lineament = Lineament::find($id);
		if(Gate::denies('update-lineament', $lineament)) {
			abort(403);
		}
		$lineament->note = $request->post('note');
		$lineament->mark = $request->post('mark');
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
}
