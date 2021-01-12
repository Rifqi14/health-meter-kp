<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssessmentQuestion extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    public function workforcegroup()
    {
        return $this->hasMany(AssessmentQuestionWorkforceGroup::class, 'assessment_question_id');
    }
    public function site()
    {
        return $this->hasMany(AssessmentQuestionSite::class, 'assessment_question_id');
    }
    public function parent()
    {
        return $this->belongsTo(AssessmentQuestion::class, 'question_parent_code');
    }
    public function answer()
    {
        return $this->hasMany(AssessmentAnswer::class, 'assessment_question_id');
    }
    public function answercode()
    {
        return $this->belongsTo(AssessmentAnswer::class, 'answer_parent_code');
    }
}