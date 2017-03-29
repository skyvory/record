<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVndbreleaseidVnTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(Schema::hasTable('vn')) {
            if(!Schema::hasColumn('vn', 'vndb_release_id')) {
                Schema::table('vn', function(Blueprint $table) {
                    $table->integer('vndb_release_id')->nullable();
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
            if(Schema::hasColumn('vn', 'vndb_release_id')) {
                Schema::table('vn', function(Blueprint $table) {
                    $table->dropColumn(['vndb_release_id']);
                });
            }
        }
    }
}
