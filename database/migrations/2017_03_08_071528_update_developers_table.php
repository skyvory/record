<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateDevelopersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(Schema::hasTable('developers')) {
            Schema::table('developers', function(Blueprint $table) {
                $table->renameColumn('name_jp', 'original');
                $table->renameColumn('name_en', 'romaji');
                $table->string('furi')->nullable()->comment('Hiragana/katakana depending on the original name between native Japanese or foreign name. Null value is aceptable mainly for original name that is already in hiragana/katakana.');
            });
        }
        if(Schema::hasTable('developers')) {
            Schema::table('developers', function(Blueprint $table) {
                $table->string('original')->nullable(false)->default(false)->change();
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
        if(Schema::hasTable('developers')) {
            Schema::table('developers', function(Blueprint $table) {
                $table->string('original')->nullable()->default('')->change();
            });
        }
        if(Schema::hasTable('developers')) {
            Schema::table('developers', function(Blueprint $table) {
                $table->dropColumn(['furi']);
                $table->renameColumn('romaji', 'name_en');
                $table->renameColumn('original', 'name_jp');
            });
        }
    }
}
