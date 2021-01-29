<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('workforce_id')->nullable();
            $table->date('date')->nullable();
            $table->unsignedBigInteger('attendance_description_id')->nullable();
            $table->softDeletes()->nullable();
            $table->timestamps();

            $table->foreign('workforce_id')->references('id')->on('workforces')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('attendance_description_id')->references('id')->on('attendance_descriptions')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendances');
    }
}