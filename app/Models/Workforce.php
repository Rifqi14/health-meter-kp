<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Workforce extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    public function updatedby()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    public function workforcegroup()
    {
        return $this->belongsTo(WorkforceGroup::class, 'workforce_group_id');
    }
    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }
    public function title()
    {
        return $this->belongsTo(Title::class);
    }
    public function site()
    {
        return $this->belongsTo(Site::class);
    }
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    public function subdepartment()
    {
        return $this->belongsTo(SubDepartment::class, 'sub_department_id');
    }
    public function guarantor()
    {
        return $this->belongsTo(Guarantor::class);
    }
    public function user()
    {
        return $this->hasOne(User::class);
    }
    public function assessmentresult()
    {
        return $this->hasMany(AssessmentResult::class, 'workforce_id');
    }
}