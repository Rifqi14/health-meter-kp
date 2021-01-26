<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateControlCardLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('control_card_logs', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('control_card_id');
            $table->foreign('control_card_id')->references('id')->on('control_cards')->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedBigInteger('nid');
            $table->foreign('nid')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');

            $table->date('date');

            $table->unsignedBigInteger('examination_evaluation_id')->nullable();
            $table->foreign('examination_evaluation_id')->references('id')->on('examination_evaluations')->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedBigInteger('examination_evaluation_level_id')->nullable();
            $table->foreign('examination_evaluation_level_id')->references('id')->on('examination_evaluation_levels')->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');

            $table->string('input_status');
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
        Schema::dropIfExists('control_card_logs');
    }
}
