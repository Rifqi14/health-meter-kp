<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCoveringLettersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('covering_letters', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('type')->nullable();
            $table->string('number')->nullable();
            $table->date('letter_date')->nullable();

            $table->unsignedBigInteger('workforce_id')->nullable();
            $table->foreign('workforce_id')->references('id')->on('workforces')->onUpdate('cascade')->onDelete('restrict');

            $table->unsignedBigInteger('patient_id')->nullable();
            $table->foreign('patient_id')->references('id')->on('patients')->onUpdate('cascade')->onDelete('restrict');

            $table->unsignedBigInteger('patient_site_id')->nullable();
            $table->foreign('patient_site_id')->references('id')->on('sites')->onUpdate('cascade')->onDelete('restrict');

            $table->unsignedBigInteger('doctor_id')->nullable();
            $table->foreign('doctor_id')->references('id')->on('doctors')->onUpdate('cascade')->onDelete('restrict');

            $table->unsignedBigInteger('doctor_site_id')->nullable();
            $table->foreign('doctor_site_id')->references('id')->on('sites')->onUpdate('cascade')->onDelete('restrict');

            $table->unsignedBigInteger('partner_id')->nullable();
            $table->foreign('partner_id')->references('id')->on('partners')->onUpdate('cascade')->onDelete('restrict');

            $table->unsignedBigInteger('speciality_id')->nullable();
            $table->foreign('speciality_id')->references('id')->on('specialities')->onUpdate('cascade')->onDelete('restrict');

            $table->unsignedBigInteger('referral_doctor_id')->nullable();
            $table->foreign('referral_doctor_id')->references('id')->on('doctors')->onUpdate('cascade')->onDelete('restrict');
            
            $table->unsignedBigInteger('referral_partner_id')->nullable();
            $table->foreign('referral_partner_id')->references('id')->on('partners')->onUpdate('cascade')->onDelete('restrict');

            $table->unsignedBigInteger('referral_speciality_id')->nullable();
            $table->foreign('referral_speciality_id')->references('id')->on('specialities')->onUpdate('cascade')->onDelete('restrict');

            $table->unsignedBigInteger('consultation_id')->nullable();
            $table->foreign('consultation_id')->references('id')->on('health_consultations')->onUpdate('cascade')->onDelete('restrict');

            $table->unsignedBigInteger('medicine_id')->nullable();
            $table->foreign('medicine_id')->references('id')->on('medicines')->onUpdate('cascade')->onDelete('restrict');

            $table->unsignedBigInteger('using_rule_id')->nullable();
            $table->foreign('using_rule_id')->references('id')->on('using_rules')->onUpdate('cascade')->onDelete('restrict');

            $table->string('amount')->nullable();
            $table->text('description')->nullable();
            $table->integer('print_status')->nullable();
            $table->string('document_link')->nullable();
            $table->string('status')->nullable();
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
        Schema::dropIfExists('covering_letters');
    }
}
