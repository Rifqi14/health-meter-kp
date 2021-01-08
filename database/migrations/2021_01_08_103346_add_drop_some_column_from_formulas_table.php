<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDropSomeColumnFromFormulasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('formulas', function (Blueprint $table) {
            $table->dropColumn(['operation', 'result']);
            $table->integer('updated_by')->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('formulas', function (Blueprint $table) {
            $table->string('operation')->nullable();
            $table->string('result')->nullable();
            $table->dropColumn('updated_by');
            $table->dropSoftDeletes();
        });
    }
}