<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropVnRelationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::drop('vn_relations');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Fall of this logic lies in precognition conflict where simultaneus request of next group_id could cause confusion of unrelated vn_id getting mixed into the same group
        Schema::create('vn_relations', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('group_id')->unsigned();
            $table->integer('vn_id')->unsigned();
            $table->timestamps();
        });
    }
}
