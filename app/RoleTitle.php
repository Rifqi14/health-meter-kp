<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RoleTitle extends Model
{
    protected $guarded = [];
    public function role()
    {
        return $this->hasOne('App\Role', 'id', 'role_id');
    }
}
