<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAgencySitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agency_sites', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('agency_id')->nullable();
            $table->unsignedBigInteger('site_id')->nullable();
            $table->integer('updated_by')->nullable();
            $table->softDeletes()->nullable();
            $table->timestamps();
            $table->foreign('agency_id')->references('id')->on('agencies')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('site_id')->references('id')->on('sites')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('agency_sites');
    }
}