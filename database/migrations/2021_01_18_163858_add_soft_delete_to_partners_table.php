<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSoftDeleteToPartnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('partners', function (Blueprint $table) {
            $table->dropForeign(['id_partner_category']);
            $table->dropColumn(['status', 'id_partner_category']);
            $table->unsignedBigInteger('partner_category_id')->nullable();
            $table->foreign('partner_category_id')->references('id')->on('partner_categories')->onUpdate('restrict')->onDelete('restrict');
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
        Schema::table('partners', function (Blueprint $table) {
            $table->unsignedBigInteger('id_partner_category')->nullable();
            $table->foreign('id_partner_category')->references('id')->on('partners')->onUpdate('cascade')->onDelete('cascade');
            $table->dropForeign(['partner_category_id']);
            $table->dropColumn(['partner_category_id']);
            $table->integer('status')->nullable();
            $table->dropSoftDeletes();
        });
    }
}
