<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormulaDetail extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    public function question()
    {
        return $this->belongsTo(AssessmentQuestion::class, 'assessment_question_id');
    }
    public function answer()
    {
        return $this->belongsTo(AssessmentAnswer::class, 'assessment_answer_id');
    }
    public function formula()
    {
        return $this->belongsTo(Formula::class, 'formula_id');
    }
}