<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssessmentsHistoryTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('assessments_history', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('assessment_id');
			$table->integer('revision_sequence');
			$table->dateTime('modified_date');
			$table->integer('vn_id')->nullable();
			$table->integer('user_id')->nullable();
			$table->datetime('date_start')->nullable();
			$table->datetime('date_end')->nullable();
			$table->string('node')->nullable();
			$table->char('score_story')->nullable();
			$table->char('score_naki')->nullable();
			$table->char('score_nuki')->nullable();
			$table->char('score_comedy')->nullable();
			$table->char('score_graphic')->nullable();
			$table->integer('score_all')->nullable();
			$table->string('savable', 16)->nullable();
			$table->boolean('archive_savedata')->nullable();
			$table->string('status')->nullable();
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
		Schema::drop('assessments_history');
	}
}
