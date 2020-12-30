<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignToDoctorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('doctors', function (Blueprint $table) {
            $table->unsignedBigInteger('id_partner')->nullable();
            $table->unsignedBigInteger('id_speciality')->nullable();

            $table->foreign('id_partner')->references('id')->on('partners')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_speciality')->references('id')->on('specialities')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('doctors', function (Blueprint $table) {
            $table->dropForeign(['id_partner', 'id_speciality']);
            $table->dropColumn(['id_partner', 'id_speciality']);
        });
    }
}