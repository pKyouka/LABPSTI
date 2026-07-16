<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ResetRequestUpdated extends Notification implements ShouldQueue
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
        $status = $this->vmBorrowRequest->reset_status;

        return [
            'vm_borrow_request_id' => $this->vmBorrowRequest->id,
            'status' => $status,
            'reset_rejection_reason' => $this->vmBorrowRequest->reset_rejection_reason ?? '',
            'type' => 'reset_request_updated',
        ];
    }
}
