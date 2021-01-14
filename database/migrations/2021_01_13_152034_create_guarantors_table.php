<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGuarantorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guarantors', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('site_id')->nullable();
            $table->string('position_code')->nullable()->unique();
            $table->string('nid')->nullable();
            $table->integer('updated_by')->nullable();
            $table->softDeletes()->nullable();
            $table->timestamps();

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
        Schema::dropIfExists('guarantors');
    }
}