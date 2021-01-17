<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCrudToRoleMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('role_menus', function (Blueprint $table) {
            $table->string('create')->nullable();
            $table->string('read')->nullable();
            $table->string('update')->nullable();
            $table->string('delete')->nullable();
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
            $table->dropColumn('create');
            $table->dropColumn('read');
            $table->dropColumn('update');
            $table->dropColumn('delete');
        });
    }
}
