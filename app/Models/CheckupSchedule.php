<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class CheckupSchedule extends Model
{
    protected $guarded = [];

    public function updatedby()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}