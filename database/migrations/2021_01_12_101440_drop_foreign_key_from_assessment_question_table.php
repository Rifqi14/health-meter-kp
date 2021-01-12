<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropForeignKeyFromAssessmentQuestionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assessment_questions', function (Blueprint $table) {
            $table->dropForeign(['workforce_group_id']);
            $table->dropForeign(['site_id']);

            $table->dropColumn(['workforce_group_id', 'site_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assessment_questions', function (Blueprint $table) {
            $table->unsignedBigInteger('workforce_group_id')->nullable();
            $table->unsignedBigInteger('site_id')->nullable();

            $table->foreign('workforce_group_id')->references('id')->on('workforce_groups')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('site_id')->references('id')->on('sites')->onUpdate('cascade')->onDelete('cascade');
        });
    }
}