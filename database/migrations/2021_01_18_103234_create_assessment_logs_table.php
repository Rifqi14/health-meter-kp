<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssessmentLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assessment_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('workforce_id')->nullable();
            $table->unsignedBigInteger('assessment_id')->nullable();
            $table->date('date')->nullable();
            $table->unsignedBigInteger('assessment_answer_id')->nullable();
            $table->string('status')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('workforce_id')->references('id')->on('workforces')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('assessment_id')->references('id')->on('assessments')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('assessment_answer_id')->references('id')->on('assessment_answers')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assessment_logs');
    }
}