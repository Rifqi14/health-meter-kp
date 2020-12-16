<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCheckupDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('checkup_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('checkup_id')->nullable();
            $table->unsignedBigInteger('medical_detail_id')->nullable();
            $table->text('value')->nullable();
            $table->foreign('checkup_id')->references('id')->on('checkups')->onDelete('cascade');
            $table->foreign('medical_detail_id')->references('id')->on('medical_details')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('checkup_details');
    }
}
