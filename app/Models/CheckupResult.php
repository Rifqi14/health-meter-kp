<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CheckupResult extends Model
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
        return $this->belongsTo(Patient::class, 'patient_id');
    }
    public function patientsite()
    {
        return $this->belongsTo(Site::class, 'patient_site_id');
    }
    public function checkupschedule()
    {
        return $this->belongsTo(CheckupSchedule::class, 'checkup_schedule_id');
    }
    public function partner()
    {
        return $this->belongsTo(Partner::class, 'partner_id');
    }
    public function examinationtype()
    {
        return $this->belongsTo(ExaminationType::class, 'examination_type_id');
    }
    public function evaluation()
    {
        return $this->belongsTo(ExaminationEvaluation::class, 'examination_evaluation_id');
    }
    public function evaluationlevel()
    {
        return $this->belongsTo(ExaminationEvaluationLevel::class, 'examination_evaluation_level_id');
    }
    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }
    public function doctorsite()
    {
        return $this->belongsTo(Site::class, 'doctor_site_id');
    }
}