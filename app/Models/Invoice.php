<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $guarded = [];
    public function partner() {
        return $this->hasOne('App\Models\Partner', 'id', 'partner_id');
    }
    public function document(){
        return $this->hasMany('App\Models\InvoiceDocument');
    }
}
