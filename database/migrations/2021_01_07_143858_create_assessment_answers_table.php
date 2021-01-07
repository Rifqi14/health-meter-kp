<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssessmentAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assessment_answers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('assessment_question_id')->nullable();
            $table->string('answer_type')->nullable();
            $table->text('description')->nullable();
            $table->integer('rating')->nullable();
            $table->integer('updated_by')->nullable();
            $table->text('information')->nullable();
            $table->softDeletes()->nullable();
            $table->timestamps();

            $table->foreign('assessment_question_id')->references('id')->on('assessment_questions')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assessment_answers');
    }
}