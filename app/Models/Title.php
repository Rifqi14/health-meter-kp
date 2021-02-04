<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Klaravel\Ntrust\Traits\NtrustUserTrait;
class Title extends Model
{
    use SoftDeletes;
    use NtrustUserTrait;

    protected static $roleProfile = 'title';
    protected $guarded = [];
    public function EmployeeMovement()
    {
        return $this->hasMany('App\Models\EmployeeMovement');
    }
    public function roles()
    {
        return $this->belongsToMany('App\Role', 'role_titles', 'title_id', 'role_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    public function site()
    {
        return $this->hasOne('App\Models\Site', 'id', 'site_id');
    }
    public function agency()
    {
        return $this->hasOne('App\Models\Agency', 'id', 'agency_id');
    }
    public function department()
    {
        return $this->hasOne('App\Models\Department', 'id', 'department_id');
    }
    public function sub_department()
    {
        return $this->hasOne('App\Models\SubDepartment', 'id', 'sub_department_id');
    }
    public function t_schedulemaker()
    {
        return $this->hasMany(Title::class, 'schedule_maker_title_id');
    }
    public function t_firstapproval()
    {
        return $this->hasMany(Title::class, 'first_approval_title_id');
    }
    public function t_secondapproval()
    {
        return $this->hasMany(Title::class, 'second_approval_title_id');
    }
}