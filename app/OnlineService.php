<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OnlineService extends Model
{
    protected $table = 'online_services';
    protected $fillable = ['user_id', 'twitter_oauth_token', 'twitter_oauth_token_secret', 'twitter_user_id', 'twitter_screen_name', 'twitter_x_auth_expires'];
}
