<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Examination extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    public function examintaion_detail()
    {
        return $this->hasMany(ExaminationType::class, 'examination_id');
    }
}