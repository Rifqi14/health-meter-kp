<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoleDashboard extends Model
{
    protected $guarded = [];
    public function dashboard() {
        return $this->hasOne('App\Models\Dashboard', 'id', 'dashboard_id');
    }
}
