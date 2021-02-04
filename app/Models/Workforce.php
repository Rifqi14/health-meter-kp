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
    public function secondarytitle()
    {
        return $this->belongsToMany(Title::class, 'title_workforce')->withTimestamps();
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
    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }
    public function placeofbirth()
    {
        return $this->belongsTo(Region::class, 'place_of_birth');
    }
    
    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }
    public function w_schedulemaker()
    {
        return $this->hasMany(Workforce::class, 'schedules_maker_id');
    }
    public function w_firstapproval()
    {
        return $this->hasMany(Workforce::class, 'first_approval_id');
    }
    public function w_secondapproval()
    {
        return $this->hasMany(Workforce::class, 'second_approval_id');
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Scope a query to check code
     *
     * @param \Illuminate\Database\Eloquent\Builder $q
     * @param mixed $code
     * @return void
     */
    public function scopeCheckCode($q, $code)
    {
        return $q->whereRaw("upper(code) = '$code'");
    }

    /**
     * Scope a query to check nid
     *
     * @param \Illuminate\Database\Eloquent\Builder $q
     * @param mixed $nid
     * @return void
     */
    public function scopeCheckNID($q, $nid)
    {
        return $q->whereRaw("upper(nid) = '$nid'");
    }
}