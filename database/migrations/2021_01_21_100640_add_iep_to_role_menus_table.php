<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIepToRoleMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('role_menus', function (Blueprint $table) {
            $table->string('import')->nullable();
            $table->string('export')->nullable();
            $table->string('print')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('role_menus', function (Blueprint $table) {
            $table->dropColumn('import');
            $table->dropColumn('export');
            $table->dropColumn('print');
        });
    }
}
