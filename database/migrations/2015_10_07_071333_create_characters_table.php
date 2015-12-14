<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCharactersTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('characters', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('vn_id')->unsigned()->nullable();
			// $table->foreign('vn_id')->references('vn')->on('id')->onDelete('cascade');
			$table->string('kanji')->nullable()->default('');
			$table->string('betsumyou')->nullable()->default('');
			$table->string('yobikata')->nullable()->default('');
			$table->smallInteger('birthmonth')->nullable();
			$table->smallInteger('birthday')->nullable();
			$table->integer('height')->unsigned()->nullable();
			$table->integer('bust')->unsigned()->nullable();
			$table->integer('waist')->unsigned()->nullable();
			$table->integer('hip')->unsigned()->nullable();
			$table->string('image')->nullable();
			$table->integer('vndb_character_id')->unsigned()->nullable();
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
		Schema::drop('characters');
	}
}
