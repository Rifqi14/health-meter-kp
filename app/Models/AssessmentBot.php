<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentBot extends Model
{
    protected $guarded = [];
    public function result()
    {
        return $this->belongsTo(AssessmentResult::class, 'assessment_result_id');
    }
}
