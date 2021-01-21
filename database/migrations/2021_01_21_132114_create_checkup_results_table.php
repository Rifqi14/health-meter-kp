<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCheckupResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('checkup_results', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('workforce_id')->nullable();
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->unsignedBigInteger('patient_site_id')->nullable();
            $table->date('date')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->unsignedBigInteger('checkup_schedule_id')->nullable();
            $table->unsignedBigInteger('partner_id')->nullable();
            $table->unsignedBigInteger('examination_type_id')->nullable();
            $table->string('result')->nullable();
            $table->string('normal_limit')->nullable();
            $table->unsignedBigInteger('examination_evaluation_id')->nullable();
            $table->unsignedBigInteger('examination_evaluation_level_id')->nullable();
            $table->unsignedBigInteger('doctor_id')->nullable();
            $table->unsignedBigInteger('doctor_site_id')->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('workforce_id')->references('id')->on('workforces')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('patient_id')->references('id')->on('patients')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('patient_site_id')->references('id')->on('sites')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('checkup_schedule_id')->references('id')->on('checkup_schedules')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('partner_id')->references('id')->on('partners')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('examination_type_id')->references('id')->on('examination_types')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('examination_evaluation_id')->references('id')->on('examination_evaluations')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('examination_evaluation_level_id')->references('id')->on('examination_evaluation_levels')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('doctor_id')->references('id')->on('doctors')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('doctor_site_id')->references('id')->on('sites')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('updated_by')->references('id')->on('users')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('checkup_results');
    }
}