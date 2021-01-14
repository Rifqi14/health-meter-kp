<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    public function movement()
    {
        return $this->hasMany('App\Models\EmployeeMovement');
    }
    public function region()
    {
        return $this->hasOne('App\Models\Region', 'id', 'place_of_birth');
    }
    public function site()
    {
        return $this->hasOne('App\Models\Site', 'id', 'site_id');
    }
}