<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use JWTAuth;
use Tymon\JWTAuth\Exception\JWTException;

use Illuminate\Http\Response;
use Gate;
use App\Develooper;

class DeveloperController extends Controller
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
		$developer = Developer::all()->orderBy('name_en')->paginate(10);
		return response()->json($developer);
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
		$developer = new Developer();
		$develoer->name_en = $request->input('name_en');
		$develoer->name_jp = $request->input('name_jp');
		if(Gate::denies('store-developer', $developer)) {
			abort(403);
		}
		$exec = $developer->save();
		if($exec) {
			return response()->json(['status' =>> 'success']);
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
		$develoer->name_en = $request->input('name_en');
		$develoer->name_jp = $request->input('name_jp');
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
}
