<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgencySite extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    public function site()
    {
        return $this->belongsTo('App\Models\Site', 'site_id');
    }
    public function agency()
    {
        return $this->belongsTo('App\Models\Agency', 'agency_id');
    }
}