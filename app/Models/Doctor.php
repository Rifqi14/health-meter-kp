<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Doctor extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    public function site()
    {
        return $this->hasOne(Site::class, 'id', 'site_id');
    }
    public function partner()
    {
        return $this->hasOne(Partner::class, 'id', 'id_partner');
    }
    public function speciality()
    {
        return $this->hasOne(Speciality::class, 'id', 'id_speciality');
    }
}