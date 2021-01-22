<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HealthConsultation extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    public function site()
    {
        return $this->belongsTo(Site::class, 'site_id');
    }

    public function patient()
    {
        return $this->hasOne('App\Models\Patient', 'id', 'patient_id');
    }

    public function doctor()
    {
        return $this->hasOne('App\Models\Doctor', 'id', 'doctor_id');
    }

    public function site_doctor()
    {
        return $this->hasOne('App\Models\Site', 'id', 'site_doctor_id');
    }

    public function site_patient()
    {
        return $this->hasOne('App\Models\Site', 'id', 'site_patient_id');
    }

    public function diagnose()
    {
        return $this->hasOne('App\Models\Diagnosis', 'id', 'diagnose_id');
    }
}
