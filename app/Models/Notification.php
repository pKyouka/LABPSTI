<?php

namespace App\Models;

use Illuminate\Notifications\DatabaseNotification;

class Notification extends DatabaseNotification
{
    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeForAdmin($query)
    {
        return $query->whereIn('type', [
            \App\Notifications\ServiceRequestReceived::class,
            \App\Notifications\ResetRequestReceived::class,
            \App\Notifications\VmBorrowRequestReceived::class,
        ]);
    }

    public function scopeForStudent($query)
    {
        return $query->whereIn('type', [
            \App\Notifications\ServiceRequestUpdated::class,
            \App\Notifications\ResetRequestUpdated::class,
            \App\Notifications\VmBorrowRequestUpdated::class,
        ]);
    }
}
