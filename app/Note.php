<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
		protected $table = 'notes';

		protected function user() {
			return $this->belongsTo('App\User', 'user_id');
		}
		protected function vn() {
			return $this->belongsTo('App\Vn', 'vn_id');
		}
}
