<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    protected $guarded = [];

    public function site()
    {
        return $this->hasOne(Site::class, 'id', 'site_id');
    }
}