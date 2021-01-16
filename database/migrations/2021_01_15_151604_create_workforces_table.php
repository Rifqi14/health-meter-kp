<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkforcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workforces', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nid')->nullable()->unique();
            $table->string('name')->nullable();
            $table->unsignedBigInteger('workforce_group_id')->nullable();
            $table->unsignedBigInteger('agency_id')->nullable();
            $table->unsignedBigInteger('title_id')->nullable();
            $table->unsignedBigInteger('site_id')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('sub_department_id')->nullable();
            $table->unsignedBigInteger('guarantor_id')->nullable();
            $table->date('start_date')->nullable();
            $table->date('finish_date')->nullable();
            $table->integer('updated_by')->nullable();
            $table->softDeletes()->nullable();
            $table->timestamps();

            $table->foreign('workforce_group_id')->references('id')->on('workforce_groups')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('agency_id')->references('id')->on('agencies')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('title_id')->references('id')->on('titles')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('site_id')->references('id')->on('sites')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('department_id')->references('id')->on('departments')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('sub_department_id')->references('id')->on('sub_departments')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('guarantor_id')->references('id')->on('guarantors')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('workforces');
    }
}