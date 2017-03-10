<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCharactersTableRenameKanjiColumn extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if(Schema::hasTable('characters')) {
			Schema::table('characters', function(Blueprint $table) {
				$table->renameColumn('kanji', 'name_original');
				$table->renameColumn('yobikata', 'name_furigana');
				$table->renameColumn('betsumyou', 'name_betsumyou');
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
		if(Schema::hasTable('characters')) {
			Schema::table('characters', function(Blueprint $table) {
				$table->renameColumn('name_betsumyou', 'betsumyou');
				$table->renameColumn('name_furigana', 'yobikata');
				$table->renameColumn('name_original', 'kanji');
			});
		}
	}
}
