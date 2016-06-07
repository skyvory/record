<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAssessmentsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if(Schema::hasTable('assessments')) {
			Schema::table('assessments', function($table) {
				$table->char('score_comedy')->after('score_nuki')->nullable()->default('');
				$table->string('savable', 16)->after('score_all')->nullable()->default('unknown');
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
		if(Schema::hasTable('assessments')) {
			Schema::table('assessments', function($table) {
				$table->dropColumn(['score_comedy', 'savable']);
			});
		}
	}
}
