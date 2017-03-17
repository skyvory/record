<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCharactersHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('characters_history')) {
            Schema::create('characters_history', function(Blueprint $table) {
                $table->increments('id');
                $table->integer('character_id');
                $table->integer('revision_sequence');
                $table->dateTime('modified_date');
                $table->integer('vn_id')->nullable();
                $table->string('name_original')->nullable();
                $table->string('name_betsumyou')->nullable();
                $table->string('name_furigana')->nullable();
                $table->smallInteger('birthmonth')->nullable();
                $table->smallInteger('birthday')->nullable();
                $table->integer('age')->nullable();
                $table->integer('height')->nullable();
                $table->integer('weight')->nullable();
                $table->integer('bust')->nullable();
                $table->integer('waist')->nullable();
                $table->integer('hip')->nullable();
                $table->string('blood_type')->nullable();
                $table->string('image')->nullable();
                $table->string('local_image')->nullable();
                $table->string('description', 6000)->nullable();
                $table->integer('vndb_character_id')->nullable();
                $table->integer('record_status')->nullable();
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
        if(Schema::hasTable('characters_history')) {
            Schema::drop('characters_history');
        }
    }
}
