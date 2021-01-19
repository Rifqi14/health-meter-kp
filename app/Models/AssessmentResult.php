<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentResult extends Model
{
    protected $guarded = [];

    public function workforce()
    {
        return $this->belongsTo(Workforce::class);
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
    public function category()
    {
        return $this->belongsTo(HealthMeter::class, 'health_meter_id');
    }
}