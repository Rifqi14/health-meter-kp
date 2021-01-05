<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExaminationEvaluationLevel extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function evaluation()
    {
        return $this->belongsTo(ExaminationEvaluation::class, 'examination_evaluation_id');
    }
}