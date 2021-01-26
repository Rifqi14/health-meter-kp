<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateControlCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('control_cards', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('status')->nullable();
            $table->date('control_date')->nullable();

            $table->unsignedBigInteger('nid')->nullable();
            $table->foreign('nid')->references('id')->on('workforces')->onUpdate('cascade')->onDelete('restrict');
            
            $table->unsignedBigInteger('checkup_result_id')->nullable();
            $table->foreign('checkup_result_id')->references('id')->on('checkup_results')->onUpdate('cascade')->onDelete('restrict');
            
            $table->unsignedBigInteger('checkup_examination_evaluation_id')->nullable();
            $table->foreign('checkup_examination_evaluation_id')->references('id')->on('examination_evaluations')->onUpdate('cascade')->onDelete('restrict');
            
            $table->unsignedBigInteger('checkup_examination_evaluation_level_id')->nullable();
            $table->foreign('checkup_examination_evaluation_level_id')->references('id')->on('examination_evaluation_levels')->onUpdate('cascade')->onDelete('restrict');
            
            $table->unsignedBigInteger('examination_evaluation_id')->nullable();
            $table->foreign('examination_evaluation_id')->references('id')->on('examination_evaluations')->onUpdate('cascade')->onDelete('restrict');
            
            $table->unsignedBigInteger('examination_evaluation_level_id')->nullable();
            $table->foreign('examination_evaluation_level_id')->references('id')->on('examination_evaluation_levels')->onUpdate('cascade')->onDelete('restrict');
            
            $table->unsignedBigInteger('nid_maker')->nullable();
            $table->foreign('nid_maker')->references('id')->on('users')->onUpdate('cascade')->onDelete('restrict');
            
            $table->unsignedBigInteger('site_maker_id')->nullable();
            $table->foreign('site_maker_id')->references('id')->on('sites')->onUpdate('cascade')->onDelete('restrict');
            
            $table->text('description')->nullable();

            $table->unsignedBigInteger('authorized_official_id')->nullable();
            $table->foreign('authorized_official_id')->references('id')->on('authorized_officials')->onUpdate('cascade')->onDelete('restrict');

            $table->unsignedBigInteger('guarantor_id')->nullable();
            $table->foreign('guarantor_id')->references('id')->on('guarantors')->onUpdate('cascade')->onDelete('restrict');

            $table->integer('approval_status')->nullable();
            $table->date('approval_date')->nullable();
            $table->string('card_control_status')->nullable();

            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users')->onUpdate('cascade')->onDelete('restrict');

            $table->SoftDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('control_cards');
    }
}
