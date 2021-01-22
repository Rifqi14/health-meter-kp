<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class CloseContact extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }
    public function workforce()
    {
        return $this->belongsTo(Workforce::class, 'workforce_id');
    }
}
