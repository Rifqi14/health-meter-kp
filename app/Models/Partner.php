<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    protected $guarded = [];
    public function site()
    {
        return $this->belongsTo(Site::class, 'site_id', 'id');
    }
    public function partnercategory()
    {
        return $this->belongsTo(PartnerCategory::class, 'id_partner_category');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }
}