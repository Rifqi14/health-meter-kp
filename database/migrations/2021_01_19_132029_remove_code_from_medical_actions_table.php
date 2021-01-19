<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveCodeFromMedicalActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('medical_actions', function (Blueprint $table) {
            $table->dropForeign(['template_id']);
            $table->dropColumn(['code','name','template_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('medical_actions', function (Blueprint $table) {
            $table->unsignedBigInteger('template_id')->nullable();
            $table->foreign('template_id')->references('id')->on('titles')->onUpdate('cascade')->onDelete('cascade');
            $table->string('code')->nullable();
            $table->string('name')->nullable();
        });
    }
}
