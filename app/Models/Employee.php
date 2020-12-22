<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
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
        return $this->hasOne(Site::class, 'id', 'site_id');
    }
}