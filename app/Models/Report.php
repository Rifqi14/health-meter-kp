<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $guarded = [];
    public function subcategory() {
        return $this->hasOne('App\Models\SubCategory', 'id', 'sub_category_id');
    }
}
