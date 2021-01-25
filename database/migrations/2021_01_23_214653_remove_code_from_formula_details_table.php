<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveCodeFromFormulaDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('formula_details', function (Blueprint $table) {
            $table->string('value')->nullable();
            $table->dropColumn('code');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('formula_details', function (Blueprint $table) {
            $table->string('code')->nullable();
            $table->dropColumn('value');
        });
    }
}
