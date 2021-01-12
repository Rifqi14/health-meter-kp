<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssessmentQuestionWorkforceGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assessment_question_workforce_groups', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('assessment_question_id')->nullable();
            $table->unsignedBigInteger('workforce_group_id')->nullable();
            $table->timestamps();

            $table->foreign('assessment_question_id')->references('id')->on('assessment_questions')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('workforce_group_id')->references('id')->on('workforce_groups')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assessment_question_workforce_groups');
    }
}