<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateVnTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(Schema::hasTable('vn')) {
            Schema::table('vn', function($table) {
                $table->string('alias', 1000)->after('title_jp')->nullable();
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
        if(Schema::hasTable('vn')) {
            Schema::table('vn', function($table) {
                $table->dropColumn(['alias']);
            });
        }
    }
}
