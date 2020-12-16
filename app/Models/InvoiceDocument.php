<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceDocument extends Model
{
    protected $guarded = [];

    public function doc()
    {
        return $this->hasOne(Document::class, 'id', 'document_id');
    }
}
