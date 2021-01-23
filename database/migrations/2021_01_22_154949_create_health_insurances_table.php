<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHealthInsurancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('health_insurances', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('cover_letter_type')->nullable();
            $table->string('letter_number')->nullable();
            $table->unsignedBigInteger('workforce_id')->nullable();
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->unsignedBigInteger('patient_site_id')->nullable();
            $table->unsignedBigInteger('letter_maker_id')->nullable();
            $table->unsignedBigInteger('letter_maker_site_id')->nullable();
            $table->unsignedBigInteger('authorized_official_id')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->unsignedBigInteger('partner_id')->nullable();
            $table->unsignedBigInteger('doctor_id')->nullable();
            $table->unsignedBigInteger('inpatient_id')->nullable();
            $table->text('description')->nullable();
            $table->date('date_in')->nullable();
            $table->date('date')->nullable();
            $table->integer('print_status')->nullable();
            $table->string('document_link')->nullable();
            $table->integer('status')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('workforce_id')->references('id')->on('workforces')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('patient_id')->references('id')->on('patients')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('patient_site_id')->references('id')->on('sites')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('letter_maker_id')->references('id')->on('workforces')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('letter_maker_site_id')->references('id')->on('sites')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('authorized_official_id')->references('id')->on('authorized_officials')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('partner_id')->references('id')->on('partners')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('doctor_id')->references('id')->on('doctors')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('inpatient_id')->references('id')->on('inpatients')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('updated_by')->references('id')->on('users')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('health_insurances');
    }
}