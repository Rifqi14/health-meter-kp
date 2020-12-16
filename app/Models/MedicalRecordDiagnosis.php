<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalRecordDiagnosis extends Model
{
    protected $guarded = [];
    public function diagnosis() {
        return $this->hasOne('App\Models\Diagnosis', 'id', 'diagnosis_id');
    }
}
