<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalAction extends Model
{
    protected $guarded = [];

    public function template() {
        return $this->hasOne('App\Models\Template', 'id', 'template_id');
    }

    public function examination()
    {
        return $this->belongsTo(ExaminationType::class, 'examination_type_id');
    }
}