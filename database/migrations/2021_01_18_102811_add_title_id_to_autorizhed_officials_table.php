<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTitleIdToAutorizhedOfficialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('authorized_officials', function (Blueprint $table) {
            $table->unsignedBigInteger('title_id')->nullable();
            $table->foreign('title_id')->references('id')->on('titles')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('authorized_officials', function (Blueprint $table) {
            $table->dropForeign(['title_id']);
            $table->dropColumn('title_id');
        });
    }
}
