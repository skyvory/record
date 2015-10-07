<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVnTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('vn', function (Blueprint $table) {
			$table->increments('id');
			$table->string('title_en')->nullable()->default('');
			$table->string('title_jp')->nullable()->default('');
			$table->string('hashtag')->nullable()->default('');
			$table->integer('developer_id')->unsigned()->default(0);
			$table->foreign('developer_id')->references('id')->on('developers')->onDelete('cascade');
			$table->date('date_release')->nullable()->default('');
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
		Schema::drop('vn');
	}
}
