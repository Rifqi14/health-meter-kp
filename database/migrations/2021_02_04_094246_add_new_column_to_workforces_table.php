<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewColumnToWorkforcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('workforces', function (Blueprint $table) {
            $table->unsignedBigInteger('place_of_birth')->nullable()->after('name');
            $table->date('birth_date')->nullable()->after('place_of_birth');
            $table->string('gender')->nullable()->after('birth_date');
            $table->string('religion')->nullable()->after('gender');
            $table->string('marriage_status')->nullable()->after('religion');
            $table->string('last_education')->nullable()->after('marriage_status');
            $table->string('blood_type')->nullable()->after('last_education');
            $table->string('rhesus')->nullable()->after('blood_type');
            $table->string('address')->nullable()->after('rhesus');
            $table->unsignedBigInteger('region_id')->nullable()->after('address');
            $table->string('phone')->nullable()->after('region_id');
            $table->string('id_card_number')->nullable()->after('phone');
            $table->string('bpjs_employment_number')->nullable()->after('id_card_number');
            $table->string('bpjs_health_number')->nullable()->after('bpjs_employment_number');

            $table->foreign('place_of_birth')->references('id')->on('regions')->onUpdate('cascade')->onDelete('set null');
            $table->foreign('region_id')->references('id')->on('regions')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('workforces', function (Blueprint $table) {
            $table->dropForeign(['place_of_birth']);
            $table->dropForeign(['region_id']);
            $table->dropColumn('place_of_birth');
            $table->dropColumn('birth_date');
            $table->dropColumn('religion');
            $table->dropColumn('marriage_status');
            $table->dropColumn('last_education');
            $table->dropColumn('gender');
            $table->dropColumn('blood_type');
            $table->dropColumn('rhesus');
            $table->dropColumn('address');
            $table->dropColumn('region_id');
            $table->dropColumn('phone');
            $table->dropColumn('id_card_number');
            $table->dropColumn('bpjs_employment_number');
            $table->dropColumn('bpjs_health_number');
        });
    }
}