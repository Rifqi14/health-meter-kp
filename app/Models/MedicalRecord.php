<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    protected $guarded = [];
    public function employee() {
        return $this->hasOne('App\Models\Employee', 'id', 'employee_id');
    }
    public function employeefamily() {
        return $this->hasOne('App\Models\EmployeeFamily', 'id', 'employee_family_id');
    }
    public function partner() {
        return $this->hasOne('App\Models\Partner', 'id', 'partner_id');
    }
    public function medicalaction() {
        return $this->hasOne('App\Models\MedicalAction', 'id', 'medical_action_id');
    }
    public function medicalrecorddiadnosis(){
        return $this->hasMany('App\Models\MedicalRecordDiagnosis');
    }
    public function medicalrecordpresciption(){
        return $this->hasMany('App\Models\MedicalRecordPresciption');
    }
}
