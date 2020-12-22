<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    protected $guarded = [];
    public function site()
    {
        return $this->belongsTo(Site::class, 'site_id', 'id');
    }
}