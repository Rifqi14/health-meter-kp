<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class ControlCardLog extends Model
{
    protected $guarded = [];

    public function updatedby()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    public function nid()
    {
        return $this->belongsTo(User::class, 'nid');
    }
    public function examinationevaluation()
    {
        return $this->belongsTo(ExaminationEvaluation::class, 'examination_evaluation_id');
    }
    public function examinationevaluationlevel()
    {
        return $this->belongsTo(ExaminationEvaluationLevel::class, 'examination_evaluation_level_id');
    }
    public function controlcard()
    {
        return $this->belongsTo(ControlCard::class, 'control_card_id');
    }
}
