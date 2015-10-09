<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
		protected $table = 'notes';

		protected function user() {
			return $this->belongsTo('App\User', 'id');
		}
		protected function vn() {
			return $this->belongsTo('App\Vn', 'id');
		}
}
