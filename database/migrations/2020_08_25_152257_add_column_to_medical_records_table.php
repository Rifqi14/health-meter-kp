<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToMedicalRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('medical_records', function (Blueprint $table) {
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->unsignedBigInteger('report_id')->nullable();
            $table->text('complaint');
            $table->text('diagnosis');
            $table->string('action');
            $table->unsignedBigInteger('partner_id')->nullable();
            $table->date('date');
            $table->string('status');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('report_id')->references('id')->on('reports')->onDelete('cascade');
            $table->foreign('partner_id')->references('id')->on('partners')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('medical_records', function (Blueprint $table) {
            $table->dropColumn('employee_id');
            $table->dropColumn('report_id');
            $table->dropColumn('complaint');
            $table->dropColumn('diagnosis');
            $table->dropColumn('action');
            $table->dropColumn('partner_id');
            $table->dropColumn('date');
            $table->dropColumn('status');
            $table->dropColumn('employee_id');
            $table->dropColumn('report_id');
            $table->dropColumn('partner_id');
        });
    }
}
