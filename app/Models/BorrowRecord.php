<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BorrowRecord extends Model
{
    protected $fillable = [
        'inventory_item_id',
        'user_name',
        'user_id_number',
        'quantity',
        'borrowed_at',
        'due_at',
        'returned_at',
        'purpose',
        'notes',
        'status',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'borrowed_at' => 'datetime',
        'due_at' => 'datetime',
        'returned_at' => 'datetime',
    ];

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class);
    }
}
