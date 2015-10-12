<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

// use JWTAuth;
// use Tymon\JWTAuth\Exceptions\JWTException;

use Illuminate\Http\Response;
use App\Vn;
use App\User;

class VnController extends Controller
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
		$title = "vN List";
		$vn = Vn::where('id', '>', '0')->orderBy('created_at', 'desc')->paginate(5);
		// $vn = Vn::all();
		return $vn->toJson();
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		// not used as of restful purpose
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		if($request->user()->isCommon()) {
			$allow = true;
		}

		if($allow == true) {
			$vn = new Vn();
			$vn->title_en = $request->post('title_en');
			$vn->title_jp = $request->post('title_jp');
			$vn->hashtag = $request->post('hashtag');
			$vn->developer_id = $request->post('developer_id');
			$vn->date_release = $request->post('date_release');
			$vn->save();
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
		try {
			$vn = Vn::find($id);
			$status_code = 200;
			$response = [
				'vn' => [
					'id' => $id,
					'title_en' => $vn->title_en,
					'title_jp' => $vn->title_jp,
					'hashtag' => $vn->hashtag,
					'developer_id' => $vn->developer_id,
					'date_release' => $vn->date_release,
					'created_at' => $vn->created_at,
					'updated_at' => $vn->updated_at,
				]
			];
		}
		catch(Exception $e) {
			$response = [
				"error" => "what?"
			];
			$status_code = 404;
		}
		finally {
			return response()->json($response, $status_code);
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
		// not used as of restful purpose
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
		if($request->user()->isCommon()) {
			$allow = true;
		}

		if($allow == true) {
			$vn = Vn::find($id);

			if($request->has('title_en')) {
				$title_en = $request->input('title_en');
				$vn->title_en = $title_en;
			}
			if($request->has('title_jp')) {
				$title_jp = $request->input('title_jp');
				$vn->title_jp = $title_jp;
			}
			if($request->has('hashtag')) {
				$hashtag = $request->input('hashtag');
				$vn->hashtag = $hashtag;
			}
			if($request->has('developer_id')) {
				$developer_id = $request->input('developer_id');
				$vn->developer_id = $developer_id;
			}
			if($request->has('date_release')) {
				$date_release = $request->input('date_release');
				$vn->date_release = $date_release;
			}
			$vn->save();
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
		if($request->user()->isCommon()) {
			$allow - true;
		}

		if($allow == true) {
			$vn = Vn::find($id);
			if($vn) {
				$vn->delete();
			}
		}
	}
}
