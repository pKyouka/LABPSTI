<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProxmoxServer extends Model
{
    protected $fillable = [
        'name',
        'host',
        'user',
        'password',
        'node',
        'storage',
        'bridge',
        'template_vmid',
        'verify_ssl',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'password' => 'encrypted',
        'template_vmid' => 'integer',
        'verify_ssl' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected $hidden = [
        'password',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Host URL dinormalisasi (tanpa trailing slash).
     */
    public function baseUrl(): string
    {
        return rtrim($this->host, '/');
    }

    public function statusLabel(): string
    {
        return $this->is_active ? 'Aktif' : 'Nonaktif';
    }

    public function statusBadgeClass(): string
    {
        return $this->is_active ? 'fluent-badge-success' : 'fluent-badge-danger';
    }
}
