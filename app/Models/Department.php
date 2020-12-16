<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $guarded = [];
    public function parent() {
        return $this->hasOne('App\Models\Department', 'id', 'parent_id');
    }
}
