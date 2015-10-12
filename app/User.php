<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

use Log;

class User extends Model implements AuthenticatableContract,
									AuthorizableContract,
									CanResetPasswordContract
{
	use Authenticatable, Authorizable, CanResetPassword;

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
	protected function assessments() {
		return $this->hasMany('App\Assessments', 'user_id');
	}
	// user has many notes
	protected function notes() {
		return $this->hasMany('App\Notes', 'user_id');
	}
	// user has many stocks
	protected function stocks() {
		return $this->hasMany('App\Stocks', 'user_id');
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
