<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    public function subdepartment()
    {
        return $this->hasMany(SubDepartment::class, 'department_id');
    }
    public function site()
    {
        return $this->hasOne('App\Models\Site', 'id', 'site_id');
    }
}