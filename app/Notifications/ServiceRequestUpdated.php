<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ServiceRequestUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    public $serviceRequest;

    public function __construct($serviceRequest)
    {
        $this->serviceRequest = $serviceRequest;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'service_request_id' => $this->serviceRequest->id,
            'vm_borrow_request_id' => $this->serviceRequest->vm_borrow_request_id,
            'old_status' => 'pending',
            'new_status' => $this->serviceRequest->status,
            'status_label' => $this->serviceRequest->statusLabel(),
            'title' => $this->serviceRequest->title,
            'category' => $this->serviceRequest->category,
            'category_label' => $this->serviceRequest->categoryLabel(),
            'admin_response' => $this->serviceRequest->admin_response ?? '',
            'type' => 'service_request_updated',
        ];
    }
}
