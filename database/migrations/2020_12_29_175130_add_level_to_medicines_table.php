<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLevelToMedicinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('medicines', function (Blueprint $table) {
            $table->string('level')->nullable();
            $table->text('description')->nullable();
            $table->integer('price')->nullable();
            $table->integer('status')->nullable();
            $table->integer('updated_by')->nullable();
            $table->softDeletes();
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
            $table->dropColumn('level');
            $table->dropColumn('description');
            $table->dropColumn('price');
            $table->dropColumn('status');
            $table->dropColumn('updated_by');
            $table->dropColumn('deleted_at');
        });
    }
}