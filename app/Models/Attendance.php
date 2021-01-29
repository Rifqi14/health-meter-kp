<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attendance extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    public function workforce()
    {
        return $this->belongsTo(Workforce::class);
    }
    public function description()
    {
        return $this->belongsTo(AttendanceDescription::class, 'attendance_description_id');
    }
}