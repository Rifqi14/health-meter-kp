<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentQuestionSite extends Model
{
    protected $guarded = [];

    public function question()
    {
        return $this->belongsTo(AssessmentQuestion::class, 'assessment_question_id');
    }
    public function site()
    {
        return $this->belongsTo(Site::class, 'site_id');
    }
}