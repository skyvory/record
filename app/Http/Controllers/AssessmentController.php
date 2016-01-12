<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

use Illuminate\Http\Response;
use Gate;
use App\Assessment;
use App\User;

class AssessmentController extends Controller
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
		$assessment = Assessment::where('user_id', $user->id)->orderBy('date_start', 'desc')->paginate(10);
		if(Gate::denies('index-assessment', $assessment)) {
			abort(403);
		}
		return response()->json($assessment);
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
		$assessment = new Assessment();
		$assessment->vn_id = $request->input('vn_id');
		$assessment->user_id = $request->user()->id;
		$assessment->date_start = $request->input('date_start');
		$assessment->date_end = $request->input('date_end');
		$assessment->score_story = $request->input('score_story');
		$assessment->score_naki = $request->input('score_naki');
		$assessment->score_nuki = $request->input('score_nuki');
		$assessment->score_graphic = $request->input('score_graphic');
		$assessment->score_all = $request->input('score_all');
		$assessment->archive_savedata = $request->input('archive_savedata');
		if(Gate::denies('store-assessment', $assessment)) {
			abort(403);
		}
		$exec = $assessment->save();
		$assessment->vndb_vn_id = Assessment::leftJoin('vn', 'vn.id', '=', 'assessments.vn_id')->select('vn.vndb_vn_id')->where('assessments.id', $assessment->id)->first()->vndb_vn_id;
		if($exec) {
			return response()->json($assessment);
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
		$assessment = Assessment::leftJoin('vn', 'vn.id', '=', 'assessments.vn_id')->select('assessments.*', 'vn.vndb_vn_id')->where('user_id', $user->id)->where('vn_id', $id)->first();
		if(Gate::denies('show-assessment', $assessment)) {
			abort(403);
		}
		if($assessment) {
			return response()->json($assessment);
		}
		else {
			return response()->json($assessment);
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
		$assessment = Assessment::find($id);
		if(Gate::denies('update-assessment', $assessment)) {
			abort(403);
		}
		$assessment->date_start = $request->input('date_start');
		$assessment->date_end = $request->input('date_end');
		$assessment->score_story = $request->input('score_story');
		$assessment->score_naki = $request->input('score_naki');
		$assessment->score_nuki = $request->input('score_nuki');
		$assessment->score_graphic = $request->input('score_graphic');
		$assessment->score_all = $request->input('score_all');
		$assessment->archive_savedata = $request->input('archive_savedata');
		$exec = $assessment->save();
		$assessment->vndb_vn_id = Assessment::leftJoin('vn', 'vn.id', '=', 'assessments.vn_id')->select('vn.vndb_vn_id')->where('assessments.id', $assessment->id)->first()->vndb_vn_id;
		if($exec) {
			return response()->json($assessment);
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
		$assessment = Assessment::find($id);
		if(Gate::denies('delete-assessment', $assessment)) {
			abort(403);
		}
		$exec = $assessment->delete();
		if($exec) {
			return response()->json(["status" => "success"]);
		}
	}
}
