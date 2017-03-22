<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('settings')) {
            Schema::create('settings', function(Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id');
                $table->string('vndb_userid')->nullable();
                $table->string('vndb_username_hash', 10000)->nullable();
                $table->string('vndb_password_hash', 10000)->nullable();
                $table->string('twitter_username')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if(Schema::hasTable('settings')) {
            Schema::drop('settings');
        }
    }
}
