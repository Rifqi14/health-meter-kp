<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormulaReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('formula_reports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('formula_id');
            $table->unsignedBigInteger('department_id');
            $table->date('report_date');
            $table->double('value',15,8);
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
            $table->foreign('formula_id')->references('id')->on('formulas')->onDelete('cascade');
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
        Schema::dropIfExists('formula_reports');
    }
}
