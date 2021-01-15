<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Site extends Model
{
    use SoftDeletes;
    protected $guarded = [];
    public function partner()
    {
        return $this->hasMany(Partner::class, 'site_id', 'id');
    }
    public function question()
    {
        return $this->hasMany(AssessmentQuestionSite::class, 'site_id');
    }
    public function agency()
    {
        return $this->belongsToMany('App\Models\Agency', 'agency_sites');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}