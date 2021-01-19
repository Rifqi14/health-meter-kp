<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveStatusFromExaminationTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('examination_types', function (Blueprint $table) {
            $table->dropColumn(['input','status']);
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
        Schema::table('examination_types', function (Blueprint $table) {
            $table->string('input')->nullable();
            $table->string('status')->nullable();
            $table->dropColumn(['code']);
        });
    }
}
