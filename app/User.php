<?php

namespace App;

// Commented bellow are removed in Laravel 5.3
// use Illuminate\Auth\Authenticatable;
// use Illuminate\Database\Eloquent\Model;
// use Illuminate\Auth\Passwords\CanResetPassword;
// use Illuminate\Foundation\Auth\Access\Authorizable;
// use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
// use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
// use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

use Log;

// Laravel 5.3 addition
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
	use Notifiable;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['name', 'email', 'password'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['password', 'remember_token'];

	// user has many assessments
	protected function assessment() {
		return $this->hasMany('App\Assessment', 'user_id');
	}
	// user has many notes
	protected function note() {
		return $this->hasMany('App\Note', 'user_id');
	}
	// user has many stocks
	protected function stock() {
		return $this->hasMany('App\Stock', 'user_id');
	}
	protected function lineament() {
		return $this->hasMany('App\Lineament');
	}

	protected function isAdmin() {
		$role = $this->role;
		if($role == "admin") {
			return true;
		}
		return false;
	}
	public function isCommon() {
		$role = $this->role;
		// Log::info($role);
		if($role == "common") {
			return true;
		}
		return false;
	}
}
