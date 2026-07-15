<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VmHost extends Model
{
    protected $table = 'vm_hosts';

    protected $fillable = [
        'name',
        'ip_address',
        'cpu_total',
        'cpu_used',
        'ram_total',
        'ram_used',
        'storage_total',
        'storage_used',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'cpu_total' => 'integer',
        'cpu_used' => 'integer',
        'ram_total' => 'integer',
        'ram_used' => 'integer',
        'storage_total' => 'integer',
        'storage_used' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get CPU usage percentage
     */
    public function cpuUsagePercent(): float
    {
        if ($this->cpu_total <= 0) {
            return 0;
        }
        return round(($this->cpu_used / $this->cpu_total) * 100, 1);
    }

    /**
     * Get RAM usage percentage
     */
    public function ramUsagePercent(): float
    {
        if ($this->ram_total <= 0) {
            return 0;
        }
        return round(($this->ram_used / $this->ram_total) * 100, 1);
    }

    /**
     * Get Storage usage percentage
     */
    public function storageUsagePercent(): float
    {
        if ($this->storage_total <= 0) {
            return 0;
        }
        return round(($this->storage_used / $this->storage_total) * 100, 1);
    }

    /**
     * Check if host is full (based on CPU, RAM, or Storage)
     */
    public function isFull(): bool
    {
        return $this->cpuUsagePercent() >= 100
            || $this->ramUsagePercent() >= 100
            || $this->storageUsagePercent() >= 100;
    }

    /**
     * Get available CPU cores
     */
    public function cpuAvailable(): int
    {
        return max(0, $this->cpu_total - $this->cpu_used);
    }

    /**
     * Get available RAM in MB
     */
    public function ramAvailable(): int
    {
        return max(0, $this->ram_total - $this->ram_used);
    }

    /**
     * Get available storage in GB
     */
    public function storageAvailable(): int
    {
        return max(0, $this->storage_total - $this->storage_used);
    }

    /**
     * Get status label for display
     */
    public function statusLabel(): string
    {
        if (!$this->is_active) {
            return 'Nonaktif';
        }
        if ($this->isFull()) {
            return 'Penuh';
        }
        return 'Tersedia';
    }

    /**
     * Get status badge class for display
     */
    public function statusBadgeClass(): string
    {
        if (!$this->is_active) {
            return 'fluent-badge-danger';
        }
        if ($this->isFull()) {
            return 'fluent-badge-danger';
        }
        return 'fluent-badge-success';
    }

    /**
     * Get connected VmBorrowRequests
     */
    public function vmBorrowRequests(): HasMany
    {
        return $this->hasMany(VmBorrowRequest::class, 'host_id');
    }
}