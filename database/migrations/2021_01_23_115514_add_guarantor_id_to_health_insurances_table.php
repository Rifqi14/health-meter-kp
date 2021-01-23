<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGuarantorIdToHealthInsurancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('health_insurances', function (Blueprint $table) {
            $table->unsignedBigInteger('guarantor_id')->nullable();
            $table->foreign('guarantor_id')->references('id')->on('guarantors')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('health_insurances', function (Blueprint $table) {
            $table->dropForeign(['guarantor_id']);
            $table->dropColumn('guarantor_id');
        });
    }
}