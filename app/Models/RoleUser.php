<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoleUser extends Model
{
    protected $table = 'role_user';
    protected $guarded = [];
    public function role()
    {
        return $this->hasMany('App\Role', 'id', 'role_id');
    }
}