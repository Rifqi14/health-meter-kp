<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCodeToSomeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('medicine_categories', function (Blueprint $table) {
            $table->string('code')->nullable();
        });
        Schema::table('medicine_groups', function (Blueprint $table) {
            $table->string('code')->nullable();
        });
        Schema::table('medicine_units', function (Blueprint $table) {
            $table->string('code')->nullable();
        });
        Schema::table('medicine_types', function (Blueprint $table) {
            $table->string('code')->nullable();
        });
        Schema::table('using_rules', function (Blueprint $table) {
            $table->string('code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('medicine_categories', function (Blueprint $table) {
            $table->dropColumn('code');
        });
        Schema::table('medicine_groups', function (Blueprint $table) {
            $table->dropColumn('code');
        });
        Schema::table('medicine_units', function (Blueprint $table) {
            $table->dropColumn('code');
        });
        Schema::table('medicine_types', function (Blueprint $table) {
            $table->dropColumn('code');
        });
        Schema::table('using_rules', function (Blueprint $table) {
            $table->dropColumn('code');
        });
    }
}