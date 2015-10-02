<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

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

    // user has many post
    public function posts() {
        return $this->hasMany('App\Posts', 'author_id');
    }

    // user has many comments
    public function comments() {
        return $this->hasMany('App\Comments', 'from_user');
    }

    public function can_post() {
        $role = $this->role;
        if($role == 'author' || $role == 'admin') {
            return true;
        }
        else {
            return false;
        }
    }

    public function is_admin() {
        $role = $this->role;
        if($role == 'admin') {
            return true;
        }
        return false;
    }
}
