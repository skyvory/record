<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssessmentsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('assessments', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('vn_id')->unsigned();
			// $table->foreign('vn_id')->references('id')->on('vn')->onDelete('cascade');
			$table->integer('user_id')->unsigned();
			$table->datetime('date_start')->nullable();
			$table->datetime('date_end')->nullable();
			$table->string('node')->nullable();
			$table->char('score_story')->nullable()->default('');
			$table->char('score_naki')->nullable()->default('');
			$table->char('score_nuki')->nullable()->default('');
			$table->char('score_graphic')->nullable()->default('');
			$table->integer('score_all')->nullable();
			$table->boolean('archive_savedata')->default(false);
			$table->string('status')->nullable()->default('');
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
		Schema::drop('assessments');
	}
}
