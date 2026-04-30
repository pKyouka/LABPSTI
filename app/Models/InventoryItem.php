<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    protected $fillable = [
        'code',
        'name',
        'category',
        'description',
        'unit',
        'total_stock',
        'available_stock',
        'location',
        'condition',
        'is_active',
    ];

    protected $casts = [
        'total_stock' => 'integer',
        'available_stock' => 'integer',
        'is_active' => 'boolean',
    ];

    public function borrowRecords()
    {
        return $this->hasMany(BorrowRecord::class);
    }
}
