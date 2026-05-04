<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

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
        'request_pdf_path',
        'approval_pdf_path',
        'approval_pdf_md5',
        'approval_pdf_sha256',
        'status',
        'approval_status',
        'approved_by',
        'approved_at',
        'ready_at',
        'pickup_at',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'borrowed_at' => 'datetime',
        'due_at' => 'datetime',
        'returned_at' => 'datetime',
        'approved_at' => 'datetime',
        'ready_at' => 'datetime',
        'pickup_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function approvalPdfIsValid(): bool
    {
        if (
            $this->approval_pdf_path === null ||
            $this->approval_pdf_md5 === null ||
            $this->approval_pdf_sha256 === null ||
            ! Storage::disk('public')->exists($this->approval_pdf_path)
        ) {
            return false;
        }

        $fullPath = Storage::disk('public')->path($this->approval_pdf_path);

        return hash_file('md5', $fullPath) === $this->approval_pdf_md5
            && hash_file('sha256', $fullPath) === $this->approval_pdf_sha256;
    }
}
