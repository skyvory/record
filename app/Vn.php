<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vn extends Model
{
	protected $table = 'vn';

	protected function developer() {
		return $this->belongsTo('App\Developer', 'developer_id');
	}
	protected function character() {
		return $this->hasMany('App\Character', 'vn_id');
	}
	protected function assessment() {
		return $this->hasMany('App\Assignment', 'vn_id');
	}
	protected function note() {
		return $this->hasMany('App\Note', 'vn_id');
	}
}
