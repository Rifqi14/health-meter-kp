<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class CheckupSchedule extends Model
{
    protected $guarded = [];

    public function updatedby()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }
    public function examinationtype()
    {
        return $this->belongsTo(ExaminationType::class, 'examination_type_id');
    }
    public function w_schedulemaker()
    {
        return $this->belongsTo(Workforce::class, 'schedules_maker_id');
    }
    public function w_firstapproval()
    {
        return $this->belongsTo(Workforce::class, 'first_approval_id');
    }
    public function w_secondapproval()
    {
        return $this->belongsTo(Workforce::class, 'second_approval_id');
    }
    public function t_schedulemaker()
    {
        return $this->belongsTo(Title::class, 'schedule_maker_title_id');
    }
    public function t_firstapproval()
    {
        return $this->belongsTo(Title::class, 'first_approval_title_id');
    }
    public function t_secondapproval()
    {
        return $this->belongsTo(Title::class, 'second_approval_title_id');
    }
}