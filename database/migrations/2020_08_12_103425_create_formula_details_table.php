<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormulaDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('formula_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('formula_id');
            $table->string('pick');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('operation');
            $table->double('value',15,8)->nullable();
            $table->foreign('formula_id')->references('id')->on('formulas')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('reference_id')->references('id')->on('formulas')->onDelete('cascade');
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
        Schema::dropIfExists('formula_details');
    }
}
