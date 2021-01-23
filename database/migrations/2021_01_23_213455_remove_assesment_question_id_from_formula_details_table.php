<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveAssesmentQuestionIdFromFormulaDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('formula_details', function (Blueprint $table) {
            $table->string('code')->nullable();
            $table->dropForeign(['assessment_question_id']);
            $table->dropColumn('assessment_question_id');
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
            $table->dropColumn('code');
            $table->unsignedBigInteger('assessment_question_id')->nullable();
            $table->foreign('assessment_question_id')->references('id')->on('assessment_questions')->onUpdate('restrict')->onDelete('restrict');
        });
    }
}
