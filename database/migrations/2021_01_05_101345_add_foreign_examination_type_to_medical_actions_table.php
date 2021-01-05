<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignExaminationTypeToMedicalActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('medical_actions', function (Blueprint $table) {
            $table->unsignedBigInteger('examination_type_id')->nullable();

            $table->foreign('examination_type_id')->references('id')->on('examination_types')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('medical_actions', function (Blueprint $table) {
            $table->dropForeign(['examination_type_id']);
            $table->dropColumn('examination_type_id');
        });
    }
}