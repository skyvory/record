<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

use Illuminate\Http\Response;
use Gate;
use App\Stock;

class StockController extends Controller
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
		$stock = Stock::where('user_id', $user->id)->orderBy('date_created', 'asc')->paginate(50);
		if(Gate::denies('index-stock', $stock)) {
			abort(403);
		}
		return response()->json($stock);
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
		$stock = new Stock();
		$stock->user_id = $request->input('user_id');
		$stock->title = $request->input('title');
		$stock->status = $request->input('status');
		$stock->queue_sequence = $request->input('queue_sequence');
		if(Gate::denies('store-stock', $stock)) {
			abort(403);
		}
		$exec = $stock->save();
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
		$stock = Stock::where('user_id', $user->id)->find($id);
		if(Gate::denies('show-stock', $stock)) {
			abort(403);
		}
		if($stock) {
			return response()->json($stock);
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
		$stock = Stock::find($id);
		if(Gate::denies('update-stock', $stock)) {
			abort(403);
		}
		$stock->title = $request->input('title');
		$stock->status = $request->input('status');
		$stock->queue_sequence = $request->input('queue_sequence');
		$exec = $stock->save();
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
		$stock = Stock::find($id);
		if(Gate::denies('delete-stock', $stock)) {
			abort(403);
		}
		$exec = $stock->delete();
		if($exec) {
			return response()->json(["status" => "success"]);
		}
	}
}
