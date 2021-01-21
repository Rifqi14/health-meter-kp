<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCheckupSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('checkup_schedules', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->unsignedBigInteger('examination_type_id')->nullable();
            $table->date('checkup_date')->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('schedules_maker_id')->nullable();
            $table->unsignedBigInteger('first_approval_id')->nullable();
            $table->unsignedBigInteger('second_approval_id')->nullable();
            $table->unsignedBigInteger('schedule_maker_title_id')->nullable();
            $table->unsignedBigInteger('first_approval_title_id')->nullable();
            $table->unsignedBigInteger('second_approval_title_id')->nullable();
            $table->integer('status')->nullable();
            $table->integer('first_approval_status')->nullable();
            $table->integer('second_approval_status')->nullable();
            $table->date('status_date')->nullable();
            $table->date('first_approval_status_date')->nullable();
            $table->date('second_approval_status_date')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('patient_id')->references('id')->on('patients')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('examination_type_id')->references('id')->on('examination_types')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('schedules_maker_id')->references('id')->on('workforces')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('first_approval_id')->references('id')->on('workforces')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('second_approval_id')->references('id')->on('workforces')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('schedule_maker_title_id')->references('id')->on('titles')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('first_approval_title_id')->references('id')->on('titles')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('second_approval_title_id')->references('id')->on('titles')->onUpdate('cascade')->onDelete('restrict');
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
        Schema::dropIfExists('checkup_schedules');
    }
}