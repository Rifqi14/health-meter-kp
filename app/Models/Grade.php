<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    protected $guarded = [];
    public function inpatient() {
        return $this->hasOne('App\Models\Inpatient', 'id', 'inpatient_id');
    }
}
