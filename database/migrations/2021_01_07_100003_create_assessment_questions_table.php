<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssessmentQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assessment_questions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('order')->nullable();
            $table->integer('is_parent')->nullable();
            $table->integer('question_parent_code')->nullable();
            $table->integer('answer_parent_code')->nullable();
            $table->string('type')->nullable();
            $table->text('description')->nullable();
            $table->string('frequency')->nullable();
            $table->date('start_date')->nullable();
            $table->date('finish_date')->nullable();
            $table->unsignedBigInteger('workforce_group_id')->nullable();
            $table->unsignedBigInteger('site_id')->nullable();
            $table->integer('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('workforce_group_id')->references('id')->on('workforce_groups')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('site_id')->references('id')->on('sites')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assessment_questions');
    }
}