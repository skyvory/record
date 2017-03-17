<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVnVnengineColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(Schema::hasTable('vn')) {
            if(!Schema::hasColumn('vn', 'game_engine')) {
                Schema::table('vn', function(Blueprint $table) {
                    $table->string('game_engine')->nullable();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if(Schema::hasTable('vn')) {
            if(Schema::hasColumn('vn', 'game_engine')) {
                Schema::table('vn', function(Blueprint $table) {
                    $table->dropColumn(['game_engine']);
                });
            }
        }
    }
}
