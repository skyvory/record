<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Character extends Model
{
		protected $table = 'characters';

		protected function vn() {
			return $this->belongsTo('App\Vn', 'vn_id');
		}
		protected function lineament() {
			return $this->hasMany('App\Lineament', 'id');
		}
}
