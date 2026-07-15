<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VmServiceRequest extends Model
{
    protected $table = 'vm_service_requests';

    protected $fillable = [
        'vm_borrow_request_id',
        'requester_id',
        'handler_id',
        'category',
        'title',
        'description',
        'port_number',
        'software_name',
        'config_path',
        'status',
        'admin_response',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function vmBorrowRequest(): BelongsTo
    {
        return $this->belongsTo(VmBorrowRequest::class, 'vm_borrow_request_id');
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handler_id');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public static function categories(): array
    {
        return [
            'open_port'       => 'Buka Port',
            'install_software'=> 'Install Software',
            'edit_config'     => 'Edit Konfigurasi',
            'add_storage'     => 'Tambah Storage',
            'add_ram'         => 'Tambah RAM',
            'add_cpu'         => 'Tambah CPU Core',
            'reset_vm'        => 'Reset VM',
            'other'           => 'Lainnya',
        ];
    }

    public function categoryLabel(): string
    {
        return self::categories()[$this->category] ?? $this->category;
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending'     => 'Menunggu',
            'in_progress' => 'Diproses',
            'completed'   => 'Selesai',
            'rejected'    => 'Ditolak',
            default       => $this->status,
        };
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'pending'     => 'fluent-badge-warning',
            'in_progress' => 'fluent-badge-info',
            'completed'   => 'fluent-badge-success',
            'rejected'    => 'fluent-badge-danger',
            default       => '',
        };
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }
}