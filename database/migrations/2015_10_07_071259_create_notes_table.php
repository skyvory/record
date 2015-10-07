<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('vn_id')->unsigned();
            $table->foreign('vn_id')->references('vn')->on('id')->onDelete('cascade');
            $table->string('interface')->nullable()->default('');
            $table->string('general')->nullable()->default('');
            $table->string('setting')->nullable()->default('');
            $table->string('side_chara')->nullable()->default('');
            $table->string('story')->nullable()->default('');
            $table->string('route')->nullable()->default('');
            $table->string('bgm')->nullable()->default('');
            $table->string('terminology')->nullable()->default('');
            $table->string('timescape')->nullable()->default('');
            $table->string('quote')->nullable()->default('');
            $table->string('other')->nullable()->default('');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('notes');
    }
}
