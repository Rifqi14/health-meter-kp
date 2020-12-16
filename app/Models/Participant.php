<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    protected $guarded = [];
    public function topic() {
        return $this->hasOne('App\Models\Topic', 'id', 'topic_id');
    }
}
