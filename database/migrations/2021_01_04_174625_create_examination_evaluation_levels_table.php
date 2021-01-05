<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExaminationEvaluationLevelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('examination_evaluation_levels', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('examination_evaluation_id')->nullable();
            $table->string('examination_level')->nullable();
            $table->integer('status')->nullable();
            $table->integer('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('examination_evaluation_id')->references('id')->on('examination_evaluations')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('examination_evaluation_levels');
    }
}