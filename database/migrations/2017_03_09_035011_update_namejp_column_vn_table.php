<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateNamejpColumnVnTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if(Schema::hasTable('vn')) {
			Schema::table('vn', function(Blueprint $table) {
				$table->renameColumn('title_jp', 'title_original');
				$table->renameColumn('title_en', 'title_romaji');
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
			Schema::table('vn', function(Blueprint $table) {
				$table->renameColumn('title_romaji', 'title_en');
				$table->renameColumn('title_original', 'title_jp');
			});
		}
	}
}
