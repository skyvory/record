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
use App\LineamentHistory;

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
	public function getLineaments(Request $request)
	{
		$user = JWTAuth::parseToken()->authenticate();
		$vn_id = $request->input('vn_id');
		if($vn_id != null) {
			$lineament = Lineament::leftJoin('characters', 'characters.id', '=', 'lineaments.character_id')->select('characters.*', 'lineaments.id as lineament_id', 'lineaments.character_id', 'lineaments.note', 'lineaments.mark')->where('user_id', $user->id)->where('vn_id', $vn_id)->orderBy('characters.created_at')->get();
			if(Gate::denies('index-lineament', $lineament)) {
				abort(403);
			}
			for($i = 0; $i < count($lineament); $i++) {
				$lineament[$i]->note = $this->decodeInput($lineament[$i]->note);
			}
			return response()->json(['data' => $lineament]);
		}
		else {
			$lineament = Lineament::where('user_id', $user->id)->orderBy('created_at')->paginate(10);
			if(Gate::denies('index-lineament', $lineament)) {
				abort(403);
			}
			$lineament->note = htmlentities($lineament->note);
			return response()->json($lineament);
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
		$check = Lineament::where('user_id', $request->user()->id)->where('character_id', $request->input('character_id'))->first();
		if($check) {
			return response()->json(['status' => 'lineament is already exist, use update method to update']);
		}
		$lineament = new Lineament();
		$lineament->user_id = $request->user()->id;
		$lineament->character_id = $request->input('character_id');
		$lineament->note = $this->decodeInput($request->input('note'));
		$lineament->mark = $request->input('mark');
		if(Gate::denies('store-lineament', $lineament)) {
			abort(403);
		}
		$exec = $lineament->save();
		if($exec) {
			return response()->json($lineament);
		}
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function getLineament($id)
	{
		$user = JWTAuth::parseToken()->authenticate();
		$lineament = Lineament::where('user_id', $user->id)->find($id);
		if(Gate::denies('show-lineament', $lineament)) {
			abort(403);
		}
		$lineament->note = $this->decodeInput($request->input('note'));
		if($lineament) {
			return response()->json($lineament);
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

		// Write history if any property change detected
		if($lineament->isDirty()) {
			if(!$this->writeHistory($lineament->id)) {
				return response()->json(['status' => 'error', 'errors' => ['someting is wrong with history logging']]);
			}
		}

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
	public function delete($id)
	{
		$lineament = Lineament::find($id);
		if(Gate::denies('delete-lineament', $lineament)) {
			abort(403);
		}

		if(!$this->writeHistory($lineament->id)) {
			return response()->json(['status' => 'error', 'errors' => ['someting is wrong with history logging']]);
		}

		$exec = $lineament->delete();
		if($exec) {
			return response()->json(["status" => "success"]);
		}
	}

	private function decodeInput($html)
	{
		$html = html_entity_decode($html);
		$html = preg_replace('#<br\s*/?>#i', "\n", $html);
		return $html;
	}

	private function writeHistory($id)
	{
		\DB::beginTransaction();
		$lineament = Lineament::find($id);

		$history = new LineamentHistory();
		$history->lineament_id = $lineament->id;
		$max_revision_sequence = LineamentHistory::select('revision_sequence')->where('lineament_id', $lineament->id)->where('user_id', $lineament->user_id)->lockForUpdate()->max('revision_sequence');
		$history->revision_sequence = $max_revision_sequence ? $max_revision_sequence + 1 : 1;
		$history->modified_date = $lineament->updated_at;
		$history->user_id = $lineament->user_id;
		$history->character_id = $lineament->character_id;
		$history->note = $lineament->note;
		$history->mark = $lineament->mark;

		$exec_history = $history->save();
		\DB::commit();
		return $exec_history;
	}
}
