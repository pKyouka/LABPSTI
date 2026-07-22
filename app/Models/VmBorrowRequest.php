<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VmBorrowRequest extends Model
{
    // Status/credentials/allocation set only via explicit controller updates (not request mass-assign).
    protected $fillable = [
        'user_id',
        'os_type',
        'os_version',
        'purpose',
        'duration_days',
        'start_date',
    ];

    protected $attributes = [
        'reset_status' => 'none',
    ];

    protected $hidden = [
        'vm_password',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'allocated_cpu' => 'integer',
        'allocated_ram' => 'integer',
        'allocated_storage' => 'integer',
        'vm_password' => 'encrypted',
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

    public function host(): BelongsTo
    {
        return $this->belongsTo(VmHost::class, 'host_id');
    }

    public function serviceRequests(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(VmServiceRequest::class, 'vm_borrow_request_id');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isOnReview(): bool
    {
        return $this->status === 'on-review';
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

    public function scopeOnReview($query)
    {
        return $query->where('status', 'on-review');
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
