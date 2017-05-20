<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOnlineServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('online_services', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('twitter_oauth_token', 500)->nullable();
            $table->string('twitter_oauth_token_secret', 500)->nullable();
            $table->string('twitter_user_id', 30)->nullable();
            $table->string('twitter_screen_name', 100)->nullable();
            $table->string('twitter_x_auth_expires', 9)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('online_services');
    }
}
