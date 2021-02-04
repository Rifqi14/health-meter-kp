<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePivotTitleWorkforceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('title_workforce', function (Blueprint $table) {
            $table->unsignedBigInteger('title_id')->nullable();
            $table->unsignedBigInteger('workforce_id')->nullable();
            $table->timestamps();
            $table->foreign('title_id')->references('id')->on('titles')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('workforce_id')->references('id')->on('workforces')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('title_workforce');
    }
}