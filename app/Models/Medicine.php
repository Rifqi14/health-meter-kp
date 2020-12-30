<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Medicine extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    public function medicine_category()
    {
        return $this->belongsTo(MedicineCategory::class, 'id_medicine_category');
    }

    public function medicine_group()
    {
        return $this->belongsTo(MedicineGroup::class, 'id_medicine_group');
    }

    public function medicine_unit()
    {
        return $this->belongsTo(MedicineUnit::class, 'id_medicine_unit');
    }

    public function medicine_type()
    {
        return $this->belongsTo(MedicineType::class, 'id_medicine_type');
    }
}