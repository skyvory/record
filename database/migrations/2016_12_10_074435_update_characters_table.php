<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCharactersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(Schema::hasTable('characters')) {
            Schema::table('characters', function($table) {
                $table->smallInteger('weight')->after('height')->nullable()->comment('in KG unit');
                $table->char('blood_type', 6)->after('hip')->nullable();
                $table->integer('age')->after('birthday')->nullable();
                $table->string('description', 8000)->after('local_image')->nullable();
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
            Schema::table('characters', function($table) {
                $table->dropColumn(['description', 'age', 'blood_type', 'weight']);
            });
        }
    }
}
