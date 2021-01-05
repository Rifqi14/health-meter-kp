<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExaminationType extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function examination()
    {
        return $this->belongsTo(Examination::class, 'examination_id');
    }

    public function evaluation()
    {
        return $this->hasMany(ExaminationEvaluation::class, 'examination_type_id');
    }
}