<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentQuestionWorkforceGroup extends Model
{
    protected $guarded = [];

    public function question()
    {
        return $this->belongsTo(AssessmentQuestion::class, 'assessment_question_id');
    }
    public function workforcegroup()
    {
        return $this->belongsTo(WorkforceGroup::class, 'workforce_group_id');
    }
}