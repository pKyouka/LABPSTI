<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabUsageRecord extends Model
{
    protected $fillable = ['user_name', 'user_id_number', 'category_id', 'start_time', 'end_time', 'purpose'];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
