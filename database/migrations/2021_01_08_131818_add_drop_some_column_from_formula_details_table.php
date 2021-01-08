<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDropSomeColumnFromFormulaDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('formula_details', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['reference_id']);
            $table->dropColumn(['category_id', 'reference_id', 'pick', 'value']);
            $table->integer('order')->nullable();
            $table->unsignedBigInteger('assessment_question_id')->nullable();
            $table->unsignedBigInteger('assessment_answer_id')->nullable();
            $table->integer('updated_by')->nullable();
            $table->softDeletes()->nullable();

            $table->foreign('assessment_question_id')->references('id')->on('assessment_questions')->onUpdated('cascade')->onDelete('cascade');
            $table->foreign('assessment_answer_id')->references('id')->on('assessment_answers')->onUpdated('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('formula_details', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('pick')->nullable();
            $table->integer('value')->nullable();

            $table->foreign('category_id')->references('id')->on('categories')->onUpdated('cascade')->onDelete('cascade');
            $table->foreign('reference_id')->references('id')->on('formulas')->onUpdated('cascade')->onDelete('cascade');

            $table->dropForeign(['assessment_question_id']);
            $table->dropForeign(['assessment_answer_id']);
            $table->dropColumn(['assessment_question_id', 'assessment_answer_id', 'order', 'updated_by']);
            $table->dropSoftDeletes();
        });
    }
}