<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
	// optional table references in case table name is different
	protected $table = 'assessments';
	// protected $fillable = ['vn_id', 'user_id', 'date_start', 'date_end', 'score_story', 'score_naki', 'score_nuki', 'score_graphic', 'score_all', 'archive_savedata'];

	protected function user() {
		return $this->belongsTo('App\User', 'id');
	}
	protected function vn() {
		return $this->belongsTo('App\Vn', 'id');
	}
}
