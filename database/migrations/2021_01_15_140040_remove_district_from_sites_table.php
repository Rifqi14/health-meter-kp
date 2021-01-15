<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveDistrictFromSitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->dropForeign(['province_id']);
            $table->dropForeign(['region_id']);
            $table->dropForeign(['district_id']);

            $table->dropColumn(['phone', 'email', 'province_id', 'region_id', 'district_id', 'address', 'postal_code', 'cover_letter', 'doctor_prescription']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->unsignedBigInteger('province_id')->nullable();
            $table->unsignedBigInteger('region_id')->nullable();
            $table->unsignedBigInteger('district_id')->nullable();

            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('postal_code')->nullable();
            $table->integer('cover_letter')->nullable();
            $table->integer('doctor_prescription')->nullable();
        });
    }
}