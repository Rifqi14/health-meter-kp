<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssessmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('assessment_date')->nullable();
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->unsignedBigInteger('assessment_question_id')->nullable();
            $table->unsignedBigInteger('assessment_answer_id')->nullable();
            $table->float('rating')->nullable();
            $table->text('description')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('assessment_question_id')->references('id')->on('assessment_questions')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('assessment_answer_id')->references('id')->on('assessment_answers')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assessments');
    }
}