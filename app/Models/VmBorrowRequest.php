<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VmBorrowRequest extends Model
{
    protected $fillable = [
        'user_id',
        'os_type',
        'os_version',
        'purpose',
        'duration_days',
        'start_date',
        'end_date',
        'status',
        'host_ip',
        'vm_username',
        'vm_password',
        'admin_notes',
        'approved_by',
        'reset_status',
        'reset_reason',
        'reset_requested_at',
        'reset_approved_by',
        'reset_rejected_by',
        'reset_rejection_reason',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'reset_requested_at' => 'datetime',
        'reset_approved_at' => 'datetime',
        'reset_rejected_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeResetPending($query)
    {
        return $query->where('reset_status', 'pending');
    }

    public static function osVersions(): array
    {
        return [
            'debian' => ['10', '11', '12'],
            'ubuntu' => ['22.04', '22.10', '23.04', '23.10', '24.04'],
        ];
    }
}
