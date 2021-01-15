<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSoftDeleteToGradesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('grades', function (Blueprint $table) {
            $table->integer('updated_by')->nullable();
            $table->softDeletes()->nullable();
            $table->dropForeign(['inpatient_id']);
            $table->dropColumn('inpatient_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('grades', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn('inpatient_id');
            $table->unsignedBigInteger('inpatient_id')->nullable();
            $table->foreign('inpatient_id')->references('id')->on('inpatients')->onUpdate('cascade')->onDelete('cascade');
        });
    }
}