<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HealthInsurance extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    public function updatedby()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    public function workforce()
    {
        return $this->belongsTo(Workforce::class);
    }
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
    public function patientsite()
    {
        return $this->belongsTo(Site::class, 'patient_site_id');
    }
    public function lettermaker()
    {
        return $this->belongsTo(Workforce::class, 'letter_maker_id');
    }
    public function lettermakersite()
    {
        return $this->belongsTo(Site::class, 'letter_maker_site_id');
    }
    public function authorizer()
    {
        return $this->belongsTo(AuthorizedOfficial::class, 'authorized_official_id');
    }
    public function partner()
    {
        return $this->belongsTo(Partner::class, 'partner_id');
    }
    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }
    public function inpatient()
    {
        return $this->belongsTo(Inpatient::class, 'inpatient_id');
    }
    public function guarantor()
    {
        return $this->belongsTo(Guarantor::class, 'guarantor_id');
    }
}