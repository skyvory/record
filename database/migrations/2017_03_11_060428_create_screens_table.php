<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScreensTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('screens',function(Blueprint $table) {
			$table->increments('id');
			$table->integer('vn_id')->unsigned();
			$table->string('original_filename');
			$table->string('local_filename');
			$table->string('alternative_image_url')->nullable();
			$table->smallInteger('screen_category')->nullable()->comment('1:title, 2:gameplay, 3:config, 4:save/load, 5:omake');
			$table->string('description', 600)->nullable();
			$table->integer('status')->default(1)->comment('1:active, 2:archived, 3:deleted');
			$table->timestamp('created_at')->useCurrent();
			$table->timestamp('updated_at')->useCurrent();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('screens');
	}
}
