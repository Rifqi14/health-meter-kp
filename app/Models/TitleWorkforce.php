<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TitleWorkforce extends Model
{
    /**
     * Define table name
     *
     * @var string
     */
    public $table = 'title_workforce';

    /**
     * Define fillable column
     *
     * @var array
     */
    public $fillable = [
        'title_id', 'workforce_id'
    ];

    public function title()
    {
        return $this->belongsTo(Title::class, 'title_id');
    }
    public function workforce()
    {
        return $this->belongsTo(Workforce::class, 'workforce_id');
    }
}