<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalTreatment extends Model
{
    protected $guarded = [];

    public function patient()
    {
        return $this->hasOne('App\Models\Patient', 'id', 'patient_id');
    }
    public function doctor()
    {
        return $this->hasOne('App\Models\Doctor', 'id', 'doctor_id');
    }
    public function consultation()
    {
        return $this->hasOne('App\Models\HealthConsultation', 'id', 'consultation_id');
    }
    public function medicalaction()
    {
        return $this->hasOne('App\Models\MedicalAction', 'id', 'medical_action_id');
    }
}
