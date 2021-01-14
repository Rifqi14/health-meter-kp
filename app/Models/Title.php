<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Klaravel\Ntrust\Traits\NtrustUserTrait;
class Title extends Model
{
    use NtrustUserTrait;

    protected static $roleProfile = 'title';
    protected $guarded = [];
    public function department() {
        return $this->hasOne('App\Models\Department', 'id', 'department_id');
    }
    public function parent() {
        return $this->hasOne('App\Models\Title', 'id', 'parent_id');
    }
    public function EmployeeMovement()
    {
        return $this->hasMany('App\Models\EmployeeMovement');
    }
    public function grade() {
        return $this->hasOne('App\Models\Grade', 'id', 'grade_id');
    }
    public function roles()
    {
        return $this->belongsToMany('App\Role', 'role_titles', 'title_id', 'role_id');
    }
}
