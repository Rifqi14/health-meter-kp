<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CoveringLetter extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    public function updatedby()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }
    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }
    public function referraldoctor()
    {
        return $this->belongsTo(Doctor::class, 'referral_doctor_id');
    }
    public function consultation()
    {
        return $this->belongsTo(HealthConsultation::class, 'consultation_id');
    }
    public function doctorsite()
    {
        return $this->belongsTo(Site::class, 'doctor_site_id');
    }
    public function medicine()
    {
        return $this->belongsTo(Medicine::class, 'medicine_id');
    }
    public function partner()
    {
        return $this->belongsTo(Partner::class, 'partner_id');
    }
    public function patientsite()
    {
        return $this->belongsTo(Site::class, 'patient_site_id');
    }
    public function referralpartner()
    {
        return $this->belongsTo(Partner::class, 'referral_partner_id');
    }
    public function speciality()
    {
        return $this->belongsTo(Speciality::class, 'speciality_id');
    }
    public function referralspeciality()
    {
        return $this->belongsTo(Speciality::class, 'referral_speciality_id');
    }
    public function usingrule()
    {
        return $this->belongsTo(UsingRule::class, 'using_rule_id');
    }
    public function workforce()
    {
        return $this->belongsTo(Workforce::class, 'workforce_id');
    }
}
