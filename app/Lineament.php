<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lineament extends Model
{
		protected $table = 'lineaments';

		protected function character() {
			return $this->belongsTo('App\Character', 'character_id');
		}
		protected function user() {
			return $this->belongsTo('App\User', 'user_id');
		}
}
