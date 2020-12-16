<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Title extends Model
{
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
}
