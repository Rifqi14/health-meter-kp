<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSoftDeletesToDiagnosesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('diagnoses', function (Blueprint $table) {
            $table->unsignedBigInteger('diagnoses_category_id')->nullable();
            $table->string('sub_category')->nullable();
            $table->string('english_name')->nullable();
            $table->integer('updated_by')->nullable();
            $table->softDeletes()->nullable();

            $table->foreign('diagnoses_category_id')->references('id')->on('diagnoses_categories')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('diagnoses', function (Blueprint $table) {
            $table->dropForeign(['diagnoses_category_id']);
            $table->dropColumn('diagnoses_category_id');
            $table->dropColumn('sub_category');
            $table->dropColumn('english_name');
            $table->dropColumn('updated_by');
            $table->dropSoftDeletes();
        });
    }
}