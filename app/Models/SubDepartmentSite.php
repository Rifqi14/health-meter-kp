<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubDepartmentSite extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    public function site()
    {
        return $this->belongsTo('App\Models\Site', 'site_id');
    }
    public function subdepartment()
    {
        return $this->belongsTo('App\Models\SubDepartment', 'sub_department_id');
    }
}
