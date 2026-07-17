<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class VmBorrowRequestReceived extends Notification
{
    use Queueable;

    public $vmBorrowRequest;

    public function __construct($vmBorrowRequest)
    {
        $this->vmBorrowRequest = $vmBorrowRequest;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'vm_borrow_request_id' => $this->vmBorrowRequest->id,
            'user_name' => $this->vmBorrowRequest->user->display_name ?? $this->vmBorrowRequest->user->name,
            'os_info' => strtoupper($this->vmBorrowRequest->os_type) . ' ' . $this->vmBorrowRequest->os_version,
            'type' => 'vm_borrow_request_received',
        ];
    }
}