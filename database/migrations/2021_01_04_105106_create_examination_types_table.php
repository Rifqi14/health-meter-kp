<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExaminationTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('examination_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('examination_id')->nullable();
            $table->string('name')->nullable();
            $table->string('input')->nullable();
            $table->integer('status')->nullable();
            $table->integer('updated_by')->nullable();
            $table->softDeletes()->nullable();
            $table->timestamps();

            $table->foreign('examination_id')->references('id')->on('examinations')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('examination_types');
    }
}