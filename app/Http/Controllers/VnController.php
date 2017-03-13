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
use App\VnGroup;
use App\VnGroupRelation;
use App\Http\Controllers\ExtensionPlus;

use Image;

class VnController extends Controller
{
	use ExtensionPlus;

	public function __construct() {
		$this->middleware('jwt.auth', ['except' => ['authenticate']]);
	}

	function getVns(Request $request)
	{
		$title = "VN List";
		$user = JWTAuth::parseToken()->authenticate();
		$per_page = $request->has('limit') ? $request->input('limit') : 10;
		$search_query = $request->has('filter') ? $request->input('filter') : null;
		$search_query = explode(" ", $search_query);

		$vns = Vn::select('vn.*', 'developers.original as developer_original', 'developers.romaji as developer_romaji')->leftJoin('developers', 'developers.id', '=', 'vn.developer_id');
		if($search_query) {
			foreach($search_query as $q) {
				$vns = $vns->where(function($query) use ($q) {
					$query->where('title_original', 'like', '%' . $q . '%')
					->orwhere('title_romaji', 'like', '%' . $q . '%');
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
			$vn->title_original = $request->input('title_original');
			$vn->title_romaji = $request->input('title_romaji');
			$vn->alias = $request->input('alias');
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

	// Parent means the target vn_id of which the child is going to be attached to. Conscise example is with child id being the VN in update page while the parent id would go to related_vn_id field
	private function relateVn($parent_vn_id, $child_vn_id) {
		if(!empty($parent_vn_id) && !empty($child_vn_id)) {
			
			$distinct_group_count = VnGroupRelation::where('vn_id', $parent_vn_id)->orWhere('vn_id', $child_vn_id)->groupBy('vn_group_id')->get()->count();
			if($distinct_group_count > 1)
				throw new \Symfony\Component\HttpKernel\Exception\ConflictHttpException('Grouping a VN into multiple group is not supported.');

			$existing_relations = VnGroupRelation::where('vn_id', $parent_vn_id)->orWhere('vn_id', $child_vn_id)->get();
			\DB::beginTransaction();
			try {
				// The case where there's no existing group yet
				if($existing_relations->count() < 1) {
					$group = new VnGroup();
					$group->save();
					$group_id = $group->id;
				}
				else {
					// Pointing into the first array of existing_relations will definitely getting the right group id even with only one record returned by eloquent
					$group_id = $existing_relations[0]['vn_group_id'];
				}

				// Parent check and insert if null
				$parent_existence = VnGroupRelation::where('vn_id', $parent_vn_id)->where('vn_group_id', $group_id)->first();
				if($parent_existence === null) {
					$parent = new VnGroupRelation();
					$parent->vn_id = $parent_vn_id;
					$parent->vn_group_id = $group_id;
					$parent->save();
				}

				// Child check and insert if null
				$child_existence = VnGroupRelation::where('vn_id', $child_vn_id)->where('vn_group_id', $group_id)->first();
				if($child_existence === null) {
					$child = new VnGroupRelation();
					$child->vn_id = $child_vn_id;
					$child->vn_group_id = $group_id;
					$child->save();
				}
			} catch (\Exception $e) {
				\DB::rollback();
				throw($e);
			}

			\DB::commit();
			return true;
		}
	}

	public function removeRelation(Request $request)
	{
		$this->validate($request, [
			'vn_group_id' => 'required|integer|min:1',
			'vn_id' => 'required|integer|min:1'
		]);

		$vn_group_id = $request->input('vn_group_id');
		$vn_id = $request->input('vn_id');

		$relation = VnGroupRelation::where('vn_group_id', $vn_group_id)->where('vn_id', $vn_id)->first();

		if($relation)
			$exec = VnGroupRelation::where('vn_group_id', $vn_group_id)->where('vn_id', $vn_id)->delete();
		else
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('No record found with proposed qualifier.');
		if($exec)
			return response()->json(["status" => "success"]);
		else
			throw new \Symfony\Component\HttpKernel\Exception\HttpException('Unknown database error.');
	}

	/**
	 * Retrieve the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function getVn($id)
	{
		try {
			$vn = Vn::find($id);
			$vn_simplified = [];
			if($vn) {
				$vn_group_id = VnGroupRelation::where('vn_id', $id)->first()['vn_group_id'];
				$relations = VnGroupRelation::select('vn_group_id as group_id', 'vn.*')
				->join('vn', 'vn.id', '=', 'vn_group_relations.vn_id')
				->where('vn_group_relations.vn_group_id', $vn_group_id)
				->where('vn_group_relations.vn_id', '!=', $id)
				->get();

				$vn_simplified = [
					'id' => $id,
					'title_original' => $vn->title_original,
					'title_romaji' => $vn->title_romaji,
					'alias' => $vn->alias,
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
				'data' => $vn_simplified
				);
			return response()->json($compact);
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

			if($request->has('title_original')) {
				$title_original = $request->input('title_original');
				$vn->title_original = $title_original;
			}
			if($request->has('title_romaji')) {
				$title_romaji = $request->input('title_romaji');
				$vn->title_romaji = $title_romaji;
			}
			if($request->has('alias')) {
				$alias = $request->input('alias');
				$vn->alias = $alias;
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
	public function delete(Request $request, $id)
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

	public function refreshCover($id)
	{
		$vn = Vn::find($id);

		$url = $vn->image;
		$existing_local_filename = $vn->local_image;
		$local_filename = "";
		if($url) {
			$filename = basename($url);
			$local_filename = $id . "_" . $filename;
			$save = $this->saveRemoteImage($url, 'reallocation/cover/' . $local_filename);
			if($save && $local_filename != $existing_local_filename) {
				$vn->local_image = $local_filename;
				$vn->save();
			}
		}

		return response()->json(["status" => "success"]);
	}

	public function storeScreenshot(Request $request){
		// $vn_id = $request->input('vn_id');
		$vn_id = 0;

		if($request->hasFile('screenshot')) {
			if($request->file('screenshot')->isvalid()) {
				$file = $request->file('screenshot');
				$local_name = $vn_id . '_' . $file->getClientOriginalName();
				$file->storeAs('screen', $local_name);
				$exec_save = $file->move(public_path('/reallocation/screenshot'), $local_name);
				return $exec_save;
			}
		}

	}
}
