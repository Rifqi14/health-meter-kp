<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveCodeFromPatientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropForeign(['sub_department_id']);
            $table->dropColumn(['code','nid','department_id','sub_department_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->string('code')->nullable()->unique();
            $table->string('nid')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('sub_department_id')->nullable();
            $table->foreign('department_id')->references('id')->on('departments')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('sub_department_id')->references('id')->on('sub_departments')->onUpdate('cascade')->onDelete('cascade');
        });
    }
}
