<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class VmBorrowRequestUpdated extends Notification
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
        $statusLabels = [
            'pending' => 'Menunggu Persetujuan',
            'on-review' => 'Sedang Ditinjau',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
        ];

        return [
            'vm_borrow_request_id' => $this->vmBorrowRequest->id,
            'os_info' => strtoupper($this->vmBorrowRequest->os_type) . ' ' . $this->vmBorrowRequest->os_version,
            'status' => $this->vmBorrowRequest->status,
            'status_label' => $statusLabels[$this->vmBorrowRequest->status] ?? $this->vmBorrowRequest->status,
            'admin_notes' => $this->vmBorrowRequest->admin_notes ?? '',
            'type' => 'vm_borrow_request_updated',
        ];
    }
}
