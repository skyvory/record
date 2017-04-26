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
use App\AssessmentHistory;
use Illuminate\Pagination\Paginator;

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
	public function getAssessments(Request $request)
	{
		$user = JWTAuth::parseToken()->authenticate();
		$per_page = $request->input('limit') ? $request->input('limit') : 10;
		$current_page = $request->has('page') ? $request->input('page') : 1;
		$search_query = $request->has('search_filter') ? $request->input('search_filter') : null;
		$search_query = explode(" ", $search_query);
		$filter_vn_id = $request->has('vn_id') ? $request->input('vn_id') : null;
		$filter_status = $request->has('status_filter') ? $request->input('status_filter') : null;
		$filter_node = $request->has('node_filter') ? $request->input('node_filter') : null;
		$filter_period = $request->has('period_filter') ? $request->input('period_filter') : null;

		if($current_page) {
			// set current page programatically
			Paginator::currentPageResolver(function() use ($current_page) {
				return $current_page;
			});
		}

		$assessments = Assessment::leftJoin('vn', function($join) use ($user)
		{
			$join->on('vn.id', '=', 'assessments.vn_id');
		})
		->select('vn.title_original', 'vn.title_romaji', 'vn.hashtag', 'vn.developer_id', 'vn.date_release', 'vn.image', 'vn.local_image', 'vn.vndb_vn_id',
			'assessments.*')
		->where(function($query) use ($user) {
			$query->where('assessments.user_id', $user->id);
		})
		->where('assessments.record_status', 1)
		;

		if($filter_vn_id) {
			$assessments = $assessments->where('vn_id', $filter_vn_id);
		}

		if($filter_status) {
			switch ($filter_status) {
				case 'ongoing':
					$assessments = $assessments->whereNotIn('status', ['finished', 'halted', 'dropped']);
					break;
				case 'halted':
					$assessments = $assessments->where('status', 'halted');
					break;
				case 'finished':
					$assessments = $assessments->where('status', 'finished');
					break;
				case 'dropped':
					$assessments = $assessments->where('status', 'dropped');
					break;
				default:
					break;
			}
		}

		if($filter_node) {
			switch($filter_node) {
				case 'vn':
					$assessments = $assessments->where('node', 'vn');
					break;
				case 'h':
					$assessments = $assessments->where('node', 'h');
					break;
				case 'rpg':
					$assessments = $assessments->where('node', 'rpg');
					break;
				case 'hrpg':
					$assessments = $assessments->where('node', 'hrpg');
					break;
				default:
					break;
			}
		}

		if($filter_period && $filter_period != 'all') {
			switch($filter_period) {
				case 'month':
					$date_from = date('Y-m-d 00:00:00', strtotime('first day of this month'));
					$date_to = date('Y-m-d H:i:s', strtotime('now'));
				case 'year':
					$date_from = date('Y-m-d H:i:s', strtotime('first day of January'));
					$date_to = date('Y-m-d H:i:s', strtotime('now'));
					break;
				case 'yesteryear':
					$date_from = date('Y-m-d H:i:s', strtotime('first day of January last year'));
					$date_to = date('Y-m-d H:i:s', strtotime('last day of December last year'));
					break;
				case 'last6months':
					$date_from = date('Y-m-d H:i:s', strtotime('-6 months'));
					$date_to = date('Y-m-d H:i:s', strtotime('now'));
					break;
				default:
					break;
			}
			$assessments = $assessments->whereBetween('assessments.created_at', [$date_from, $date_to]);
		}

		if($search_query) {
			foreach ($search_query as $q) {
				$assessments = $assessments->where(function($query) use ($q) {
					$query->where('title_original', 'like', '%' . $q . '%')
					->orwhere('title_romaji', 'like', '%' . $q . '%')
					;
				});
			}
		}

		$assessments = $assessments->orderBy('assessments.created_at', 'desc')->paginate($per_page);

		if(Gate::denies('index-assessment', $assessments)) {
			// abort(403);
		}

		return response()->json($assessments);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function create(Request $request)
	{
		$allowed_node = array('unknown', 'vn', 'h', 'hrpg', 'rpg');
		if(!in_array($request->input('node'), $allowed_node)) {
			$node = 'unknown';
		}
		else {
			$node = $request->input('node');
		}

		$this->validate($request, [
			'status' => 'string|nullable|in:playing,halted,finished,dropped'
			]);

		$savable = "unknown";
		if($request->input('savable') == "yes") {
			$savable = "yes";
		}
		else if($request->input('savable') == "no") {
			$savable = "no";
		}

		$assessment = new Assessment();
		$assessment->vn_id = $request->input('vn_id');
		$assessment->user_id = $request->user()->id;
		$assessment->date_start = $request->input('date_start');
		$assessment->date_end = $request->input('date_end');
		$assessment->node = $node;
		$assessment->score_story = $request->input('score_story');
		$assessment->score_naki = $request->input('score_naki');
		$assessment->score_nuki = $request->input('score_nuki');
		$assessment->score_comedy = $request->input('score_comedy');
		$assessment->score_graphic = $request->input('score_graphic');
		$assessment->score_all = $request->input('score_all');
		$assessment->archive_savedata = $request->input('archive_savedata');
		$assessment->savable = $savable;
		$assessment->status = $request->input('status');
		$assessment->record_status = 1;
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
	public function getOneAssessment($id)
	{
		$user = JWTAuth::parseToken()->authenticate();
		$assessment = Assessment::leftJoin('vn', 'vn.id', '=', 'assessments.vn_id')->select('assessments.*', 'vn.vndb_vn_id')->where('user_id', $user->id)->where('assessments.id', $id)->whereIn('assessments.record_status', array(1,2))->first();

		// if(Gate::denies('show-assessment', $assessment)) {
		// 	abort(403);
		// }

		return response()->json($assessment);
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
		$this->validate($request, [
			'status' => 'string|nullable|in:playing,halted,finished,dropped'
			]);

		$assessment = Assessment::find($id);
		if(Gate::denies('update-assessment', $assessment)) {
			abort(403);
		}

		$allowed_node = array('unknown', 'vn', 'h', 'hrpg', 'rpg');
		if(!in_array($request->input('node'), $allowed_node)) {
			$node = 'unknown';
		}
		else {
			$node = $request->input('node');
		}
		$savable = "unknown";
		if($request->input('savable') == "yes") {
			$savable = "yes";
		}
		else if($request->input('savable') == "no") {
			$savable = "no";
		}

		$status = $request->input('status');

		// Auto status set
		if(!$assessment->date_start && !$assessment->date_end && !$assessment->status && !$status && !$request->input('date_end') && $request->input('date_start') && ($request->input('date_start') != $assessment->date_start)) {
			$status = 'playing';
		}

		// check for any change in the record
		if($assessment->date_start != $request->input('date_start') || $assessment->date_end != $request->input('date_end') || $assessment->node != $request->input('node') || $assessment->score_story != $request->input('score_story') || $assessment->score_naki != $request->input('score_naki') || $assessment->score_nuki != $request->input('score_nuki') || $assessment->score_comedy != $request->input('score_comedy') || $assessment->score_graphic != $request->input('score_graphic') || $assessment->score_all != $request->input('score_all') || $assessment->archive_savedata != $request->input('archive_savedata') || $assessment->savable != $request->input('savable') || $assessment->status != $status) {

			// Log to-be-updated record to history table
			if(!$this->writeHistory($assessment->id)) {
				return response()->json(['status' => 'error', 'errors' => ['something is wrong with logging']]);
			}

			$assessment->date_start = $request->input('date_start');
			$assessment->date_end = $request->input('date_end');
			$assessment->node = $request->input('node');
			$assessment->score_story = $request->input('score_story');
			$assessment->score_naki = $request->input('score_naki');
			$assessment->score_nuki = $request->input('score_nuki');
			$assessment->score_comedy = $request->input('score_comedy');
			$assessment->score_graphic = $request->input('score_graphic');
			$assessment->score_all = $request->input('score_all');
			$assessment->archive_savedata = $request->input('archive_savedata');
			$assessment->savable = $savable;
			$assessment->status = $status;
			$exec = $assessment->save();
			$assessment->vndb_vn_id = Assessment::leftJoin('vn', 'vn.id', '=', 'assessments.vn_id')->select('vn.vndb_vn_id')->where('assessments.id', $assessment->id)->first()->vndb_vn_id;
			if($exec) {
				return response()->json($assessment);
			}
		}
		else {
			return response()->json($assessment);
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
		$assessment = Assessment::find($id);
		if(Gate::denies('delete-assessment', $assessment)) {
			abort(403);
		}

		$this->writeHistory($assessment->id);

		$assessment->record_status = 3;
		$exec = $assessment->save();
		if($exec) {
			return response()->json(["status" => "success"]);
		}
	}

	private function writeHistory($id)
	{
		$assessment = Assessment::find($id);

		$assessment_history = new AssessmentHistory();
		$assessment_history->assessment_id = $assessment->id;
		$max_revision_sequence = AssessmentHistory::select('revision_sequence')->where('assessment_id', $assessment->id)->max('revision_sequence');
		$assessment_history->revision_sequence = $max_revision_sequence ? $max_revision_sequence + 1 : 1;
		$assessment_history->modified_date = $assessment->updated_at;
		$assessment_history->vn_id = $assessment->vn_id;
		$assessment_history->user_id = $assessment->user_id;
		$assessment_history->date_start = $assessment->date_start;
		$assessment_history->date_end = $assessment->date_end;
		$assessment_history->node = $assessment->node;
		$assessment_history->score_story = $assessment->score_story;
		$assessment_history->score_naki = $assessment->score_naki;
		$assessment_history->score_nuki = $assessment->score_nuki;
		$assessment_history->score_comedy = $assessment->score_comedy;
		$assessment_history->score_graphic = $assessment->score_graphic;
		$assessment_history->score_all = $assessment->score_all;
		$assessment_history->savable = $assessment->savable;
		$assessment_history->archive_savedata = $assessment->archive_savedata;
		$assessment_history->status = $assessment->status;
		$assessment_history->record_status = $assessment->record_status;
		$exec_history = $assessment_history->save();

		return $exec_history;
	}
}
