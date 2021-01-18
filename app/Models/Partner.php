<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Partner extends Model
{
    use SoftDeletes;
    protected $guarded = [];
    public function site()
    {
        return $this->belongsTo(Site::class, 'site_id', 'id');
    }
    public function partnercategory()
    {
        return $this->belongsTo(PartnerCategory::class, 'partner_category_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }
}