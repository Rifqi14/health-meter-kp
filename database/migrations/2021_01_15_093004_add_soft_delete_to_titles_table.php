<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSoftDeleteToTitlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('titles', function (Blueprint $table) {
            $table->string('shortname')->nullable();
            $table->integer('updated_by')->nullable();
            $table->softDeletes()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('titles', function (Blueprint $table) {
            $table->dropColumn(['shortname', 'updated_by']);
            $table->dropSoftDeletes();
        });
    }
}