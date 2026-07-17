<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ServiceRequestReceived extends Notification
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
            'requester_name' => $this->serviceRequest->requester->display_name ?? $this->serviceRequest->requester->name,
            'title' => $this->serviceRequest->title,
            'category' => $this->serviceRequest->category,
            'category_label' => $this->serviceRequest->categoryLabel(),
            'type' => 'service_request_received',
        ];
    }
}
