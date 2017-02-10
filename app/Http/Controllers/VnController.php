<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

use Illuminate\Http\Response;
use App\Vn;
use App\User;
use App\VnRelation;
use App\Http\Controllers\ExtensionPlus;

use Image;

class VnController extends Controller
{
	use ExtensionPlus;

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
		$title = "vN List";
		$user = JWTAuth::parseToken()->authenticate();
		if($request->has('filter')) {
			$limit = $request->input('limit') ? $request->input('limit') : 10;
			$q = $request->has('filter') ? $request->input('filter') : '';
			$vn = Vn::leftJoin('assessments', 'assessments.vn_id', '=', 'vn.id')
					->select('vn.*', 'assessments.date_start', 'assessments.date_end', 'assessments.node', 'assessments.score_story', 'assessments.score_naki', 'assessments.score_nuki', 'assessments.score_comedy', 'assessments.score_graphic', 'assessments.score_all', 'assessments.status')
					->where(function($query) use ($user) {
						$query->where('assessments.user_id', $user->id);
						$query->orwhere('assessments.user_id', null);
					})
					->where(function($query) use ($q) {
						$query->where('title_en', 'like', '%' . $q . '%')->orwhere('title_jp', 'like', '%' . $q . '%');
					})
					->orderBy('created_at', 'desc')->paginate($limit);
			
		}
		else {
			$limit = $request->input('limit') ? $request->input('limit') : 10;
			$vn = Vn::leftJoin('assessments', 'assessments.vn_id', '=', 'vn.id')
					->select('vn.*', 'assessments.date_start', 'assessments.date_end', 'assessments.node', 'assessments.score_story', 'assessments.score_naki', 'assessments.score_nuki', 'assessments.score_comedy', 'assessments.score_graphic', 'assessments.score_all', 'assessments.status')
					->where('assessments.user_id', $user->id)
					->orwhere('assessments.user_id', null)
					->orderBy('created_at', 'desc')
					->paginate($limit);
		}
		// $vn = Vn::all();
		return $vn->toJson();
	}

	function getVns(Request $request)
	{
		$title = "VN List";
		$user = JWTAuth::parseToken()->authenticate();
		$per_page = $request->has('limit') ? $request->input('limit') : 10;
		$search_query = $request->has('filter') ? $request->input('filter') : null;
		$search_query = explode(" ", $search_query);

		$vns = Vn::select('*');
		if($search_query) {
			foreach($search_query as $q) {
				$vns = $vns->where(function($query) use ($q) {
					$query->where('title_en', 'like', '%' . $q . '%')
					->orwhere('title_jp', 'like', '%' . $q . '%');
				});
			}
		}
		$vns = $vns->orderBy('id', 'desc')->paginate($per_page);
		
		return response()->json($vns);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create(Request $request)
	{
		if($request->user()->isCommon()) {
			$allow = true;
		}
		else {
			return "your another annoying rejection";
		}

		if($allow === true) {
			$vn = new Vn();
			$vn->title_en = $request->input('title_en');
			$vn->title_jp = $request->input('title_jp');
			$vn->hashtag = $request->input('hashtag');
			$vn->developer_id = $request->input('developer_id');
			$vn->date_release = $request->input('date_release');
			$vn->image = $request->input('image');
			$vn->vndb_vn_id = $request->input('vndb_vn_id');
			$exec = $vn->save();
			if($exec) {
				// save remote image to local
				$url = $request->input('image');
				$local_filename = null;
				if($url) {
					$filename = basename($url);
					$local_filename = $vn->id . "_" . $filename;
					// using php copy function
					// copy($url, 'reallocation/' . $filename);
					// using Intervention Image, second parameter of save method is the quality of jpg image (default to 90 if not set)
					// Image::make($url)->save('reallocation/cover/' . $local_filename, 100);
					if($this->saveRemoteImage($url, 'reallocation/cover/' . $local_filename)) {
						// save local filename to database
						$vn->local_image = $local_filename;
						$vn->save();
					}
				}

				if($request->has('related_vn_id'))
					$this->relateVn($vn->id, $request->input('related_vn_id'));

				return response()->json(["status" => "success"]);
			}
		}
	}

	private function relateVn($parent_vn_id, $child_vn_id) {
		if(!empty($parent_vn_id) && !empty($child_vn_id)) {
			\DB::beginTransaction();
			try {
				$parent_relation = VnRelation::firstOrNew(['vn_id' => $parent_vn_id]);
				if(is_null($parent_relation->group_id)) {
					$max_group_id = VnRelation::max('group_id') || 0;
					if(!$max_group_id) {
						$max_group_id++;
					}
					$parent_relation->group_id = $max_group_id;
				}
				$parent_relation->vn_id = $parent_vn_id;
				$exec_parent_relation = $parent_relation->save();

				$child_relation = VnRelation::firstOrNew(['vn_id' => $child_vn_id]);
				if(is_null($child_relation->group_id)) {
					$child_relation->group_id = $parent_relation->group_id;
				}
				$child_relation->vn_id = $child_vn_id;
				$exec_child_relation = $child_relation->save();
			} catch (\Exception $e) {
				\DB::rollback();
				throw($e);
			}
			if($exec_parent_relation && $exec_child_relation) {
				\DB::commit();
				return true;
			}
			else {
				\DB::rollback();
				throw new \Symfony\Component\HttpKernel\Exception\ConflictHttpException;
			}
		}
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
		else {
			return "xxx";
		}

		if($allow == true) {
			$vn = new Vn();
			$vn->title_en = $request->input('title_en');
			$vn->title_jp = $request->input('title_jp');
			$vn->hashtag = $request->input('hashtag');
			$vn->developer_id = $request->input('developer_id');
			$vn->date_release = $request->input('date_release');
			$vn->image = $request->input('image');
			$vn->vndb_vn_id = $request->input('vndb_vn_id');
			$exec = $vn->save();
			if($exec) {
				// save remote image to local
				$url = $request->input('image');
				$local_filename = null;
				if($url) {
					$filename = basename($url);
					$local_filename = $vn->id . "_" . $filename;
					// using php copy function
					// copy($url, 'reallocation/' . $filename);
					// using Intervention Image, second parameter of save method is the quality of jpg image (default to 90 if not set)
					// Image::make($url)->save('reallocation/cover/' . $local_filename, 100);
					if($this->saveRemoteImage($url, 'reallocation/cover/' . $local_filename)) {
						// save local filename to database
						$vn->local_image = $local_filename;
						$vn->save();
					}
				}

				return response()->json(["status" => "success"]);
			}
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
			if($vn) {
				$status_code = 200;
				$response = [
					// 'vn' => [
						'id' => $id,
						'title_en' => $vn->title_en,
						'title_jp' => $vn->title_jp,
						'hashtag' => $vn->hashtag,
						'developer_id' => $vn->developer_id,
						'date_release' => $vn->date_release,
						'created_at' => $vn->created_at,
						'updated_at' => $vn->updated_at,
						'image' => $vn->image,
						'vndb_vn_id' => $vn->vndb_vn_id,
					// ]
				];
			}
			else {
				$status_code = 200;
				$response = [
					'status' => "vn does not exist",
				];
			}
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

	public function getVn($id)
	{
		try {
			$vn = Vn::find($id);
			$res = [];
			if($vn) {
				$relation_group_id = VnRelation::where('vn_id', $id)->first()['group_id'];
				$relations = VnRelation::select('vn.*')
				->join('vn', 'vn.id', '=', 'vn_relations.vn_id')
				->where('vn_relations.group_id', $relation_group_id)
				->where('vn_relations.vn_id', '!=', $id)
				->get();

				$res = [
					'id' => $id,
					'title_en' => $vn->title_en,
					'title_jp' => $vn->title_jp,
					'hashtag' => $vn->hashtag,
					'developer_id' => $vn->developer_id,
					'date_release' => $vn->date_release,
					'created_at' => $vn->created_at,
					'updated_at' => $vn->updated_at,
					'image' => $vn->image,
					'vndb_vn_id' => $vn->vndb_vn_id,
					'relations' => $relations
				];
			}
			else {
				throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('No VN withsuch ID');
			}
		}
		catch(Exception $e) {
			throw($e);
		}
		finally {
			$compact = array(
				'data' => $res
				);
			return response()->json($compact);
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

			$url = $request->input('image');
			$existing_local_filename = $vn->local_image;
			$local_filename = "";
			if($url) {
				$filename = basename($url);
				$local_filename = $id . "_" . $filename;
				if($local_filename != $existing_local_filename) {
					// Image::make($url)->save('reallocation/cover/' . $local_filename, 100);
					$this->saveRemoteImage($url, 'reallocation/cover/' . $local_filename);
				}
			}

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
			$vn->image = $request->has('image') ? $request->input('image') : null;
			$vn->local_image = $local_filename;
			if($request->has('date_release')) {
				$date_release = $request->input('date_release');
				$vn->date_release = $date_release;
			}
			$vn->vndb_vn_id = $request->has('vndb_vn_id') ? $request->input('vndb_vn_id') : null;
			$exec = $vn->save();

			if($request->has('related_vn_id'))
				$this->relateVn($vn->id, $request->input('related_vn_id'));

			if($exec) {
				return response()->json(["status" => "success"]);
			}
		}

	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Request $request, $id)
	{
		if($request->user()->isCommon()) {
			$allow = true;
		}

		if($allow == true) {
			$vn = Vn::find($id);
			if($vn) {
				$exec = $vn->delete();
				if($exec) {
					return response()->json(["status" => "success"]);
				}
			}
		}
	}
}
