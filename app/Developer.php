<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Developer extends Model
{
		protected $table = 'developers';

		protected function vn() {
			return $this->hasMany('App\Vn', 'developer_id');
		}
}
