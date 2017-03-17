<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertStatusColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(Schema::hasTable('assessments') && !Schema::hasColumn('assessments', 'record_status')) {
            Schema::table('assessments', function(Blueprint $table) {
                $table->smallInteger('record_status')->default(1);
            });
        }

        if(Schema::hasTable('assessments_history') && !Schema::hasColumn('assessments_history', 'record_status')) {
            Schema::table('assessments_history', function(Blueprint $table) {
                $table->smallInteger('record_status');
            });
        }

        if(Schema::hasTable('characters') && !Schema::hasColumn('characters', 'record_status')) {
            Schema::table('characters', function(Blueprint $table) {
                $table->smallInteger('record_status')->default(1);
            });
        }

        if(Schema::hasTable('developers') && !Schema::hasColumn('developers', 'record_status')) {
            Schema::table('developers', function(Blueprint $table) {
                $table->smallInteger('record_status')->default(1);
            });
        }

        if(Schema::hasTable('vn') && !Schema::hasColumn('vn', 'record_status')) {
            Schema::table('vn', function(Blueprint $table) {
                $table->smallInteger('record_status')->default(1);
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
        if(Schema::hasColumn('vn', 'record_status')) {
            Schema::table('vn', function(Blueprint $table) {
                $table->dropColumn(['record_status']);
            });
        }

        if(Schema::hasColumn('developers', 'record_status')) {
            Schema::table('developers', function(Blueprint $table) {
                $table->dropColumn(['record_status']);
            });
        }

         if(Schema::hasColumn('characters', 'record_status')) {
            Schema::table('characters', function(Blueprint $table) {
                $table->dropColumn(['record_status']);
            });
        }

        if(Schema::hasColumn('assessments_history', 'record_status')) {
            Schema::table('assessments_history', function(Blueprint $table) {
                $table->dropColumn(['record_status']);
            });
        }

        if(Schema::hasColumn('assessments', 'record_status')) {
            Schema::table('assessments', function(Blueprint $table) {
                $table->dropColumn(['record_status']);
            });
        }
    }
}
