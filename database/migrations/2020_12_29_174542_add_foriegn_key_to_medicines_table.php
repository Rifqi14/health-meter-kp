<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForiegnKeyToMedicinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('medicines', function (Blueprint $table) {
            $table->unsignedBigInteger('id_medicine_category')->nullable();
            $table->unsignedBigInteger('id_medicine_group')->nullable();
            $table->unsignedBigInteger('id_medicine_unit')->nullable();
            $table->unsignedBigInteger('id_medicine_type')->nullable();

            $table->foreign('id_medicine_category')->references('id')->on('medicine_categories')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_medicine_group')->references('id')->on('medicine_groups')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_medicine_unit')->references('id')->on('medicine_units')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_medicine_type')->references('id')->on('medicine_types')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('medicines', function (Blueprint $table) {
            $table->dropForeign(['id_medicine_category']);
            $table->dropForeign(['id_medicine_group']);
            $table->dropForeign(['id_medicine_unit']);
            $table->dropForeign(['id_medicine_type']);

            $table->dropColumn('id_medicine_category');
            $table->dropColumn('id_medicine_group');
            $table->dropColumn('id_medicine_unit');
            $table->dropColumn('id_medicine_type');
        });
    }
}