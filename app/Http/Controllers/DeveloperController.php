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
	public function index(Request $request)
	{
		if($request->input('name_en')) {
			$developer = Developer::where('name_en', $request->input('name_en'))->first();
			return response()->json($developer);
		}
		// $developer = Developer::orderBy('name_en')->paginate(1000);
		// return response()->json($developer);
		$developer = Developer::orderBy('name_en')->get();
		return response()->json(['data' => $developer]);
	}

	/** DEPRECATED!
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		$developer = new Developer();
		$developer->name_en = $request->input('name_en');
		$developer->name_jp = $request->input('name_jp');
		if(Gate::denies('store-developer', $developer)) {
			abort(403);
		}
		$exec = $developer->save();
		if($exec) {
			return response()->json($developer);
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
		$developer = new Developer();
		$developer->name_en = $request->input('name_en');
		$developer->name_jp = $request->input('name_jp');
		if(Gate::denies('store-developer', $developer)) {
			abort(403);
		}
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
	public function show($id)
	{
		$developer = Developer::find($id);
		if($developer) {
			return response()->json($developer);
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
		$developer = Developer::find($id);
		if(Gate::denies('update-developer', $developer)) {
			abort(403);
		}
		$developer->name_en = $request->input('name_en');
		$developer->name_jp = $request->input('name_jp');
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
	public function destroy($id)
	{
		$developer = developer::find($id);
		if(Gate::denies('delete-developer', $developer)) {
			abort(403);
		}
		$exec = $developer->delete();
		if($exec) {
			return response()->json(['status' => 'success']);
		}
	}

	public function search(Request $request) {

	}
}
