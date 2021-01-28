<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Guarantor extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'updated_by')->withTrashed();
    }
    public function site()
    {
        return $this->belongsTo(Site::class, 'site_id')->withTrashed();
    }
    public function title()
    {
        return $this->belongsTo(Title::class, 'title_id')->withTrashed();
    }
    public function workforce()
    {
        return $this->belongsTo(Workforce::class, 'workforce_id')->withTrashed();
    }
}