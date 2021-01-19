<?php

namespace App\Models;


use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class MedicalAction extends Model
{
    protected $guarded = [];
    use SoftDeletes;
    public function user()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    public function examination()
    {
        return $this->belongsTo(ExaminationType::class, 'examination_type_id');
    }
}