<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
	protected $table = 'stocks';

	protected function user() {
		return $this->belongsTo('App\User', 'user_id');
	}
}
