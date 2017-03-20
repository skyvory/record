<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVnWebProperties extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(Schema::hasTable('vn')) {
            if(!Schema::hasColumn('vn', 'homepage') && !Schema::hasColumn('vn', 'twitter') && !Schema::hasColumn('vn', 'erogamescape_game_id')) {
                Schema::table('vn', function(Blueprint $table) {
                    $table->string('homepage')->nullable();
                    $table->string('twitter')->nullable();
                    $table->integer('erogamescape_game_id')->nullable();
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
            if(Schema::hasColumn('vn', 'twitter') && Schema::hasColumn('vn', 'homepage') && Schema::hasColumn('vn', 'erogamescape_game_id')) {
                Schema::table('vn', function(Blueprint $table) {
                    $table->dropColumn(['twitter', 'homepage', 'erogamescape_game_id']);
                });
            }
        }
    }
}
