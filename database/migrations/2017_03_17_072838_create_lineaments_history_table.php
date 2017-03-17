<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLineamentsHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('lineaments_history')) {
            Schema::create('lineaments_history', function(Blueprint $table) {
                $table->increments('id');
                $table->integer('lineament_id');
                $table->integer('revision_sequence');
                $table->dateTime('modified_date');
                $table->integer('user_id')->nullable();
                $table->integer('character_id')->nullable();
                $table->string('note')->nullable();
                $table->char('mark')->nullable();
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
        if(Schema::hasTable('lineaments_history')) {
            Schema::drop('lineaments_history');
        }
    }
}
