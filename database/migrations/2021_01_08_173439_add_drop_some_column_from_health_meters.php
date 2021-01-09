<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDropSomeColumnFromHealthMeters extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('health_meters', function (Blueprint $table) {
            $table->unsignedBigInteger('site_id')->nullable();
            $table->unsignedBigInteger('workforce_group_id')->nullable();
            $table->integer('updated_by')->nullable();
            $table->softDeletes()->nullable();

            $table->foreign('site_id')->references('id')->on('sites')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('workforce_group_id')->references('id')->on('workforce_groups')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('health_meters', function (Blueprint $table) {
            $table->dropForeign(['site_id']);
            $table->dropForeign(['workforce_group_id']);

            $table->dropColumn(['site_id', 'workforce_group_id', 'updated_by']);
            $table->dropSoftDeletes();
        });
    }
}