<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Klaravel\Ntrust\Traits\NtrustUserTrait;
class Title extends Model
{
    use SoftDeletes;
    use NtrustUserTrait;

    protected static $roleProfile = 'title';
    protected $guarded = [];
    public function EmployeeMovement()
    {
        return $this->hasMany('App\Models\EmployeeMovement');
    }
    public function roles()
    {
        return $this->belongsToMany('App\Role', 'role_titles', 'title_id', 'role_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}