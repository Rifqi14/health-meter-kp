<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    public function updatedby()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    public function workforce()
    {
        return $this->belongsTo(Workforce::class);
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
    public function inpatient()
    {
        return $this->belongsTo(Inpatient::class);
    }
}