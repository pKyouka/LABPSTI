<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\VmBorrowRequest;
use App\Models\VmServiceRequest;
use App\Notifications\ResetRequestReceived;
use App\Notifications\ResetRequestUpdated;
use App\Notifications\ServiceRequestReceived;
use App\Notifications\ServiceRequestUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class VmServiceRequestController extends Controller
{
    public function store(Request $request, VmBorrowRequest $vmBorrow)
    {
        if ($vmBorrow->status !== 'approved') {
            abort(403, 'Hanya VM yang sudah disetujui yang dapat mengajukan service request.');
        }

        $user = Auth::user();
        if (!$user->isAdmin() && $vmBorrow->user_id !== $user->id) {
            abort(403);
        }

        $data = $request->validate([
            'category'       => 'required|in:open_port,install_software,edit_config,add_storage,add_ram,add_cpu,reset_vm,other',
            'title'          => 'required|string|max:255',
            'description'    => 'required|string|max:2000',
            'port_number'    => 'nullable|integer|min:1|max:65535',
            'software_name'  => 'nullable|string|max:255',
            'config_path'    => 'nullable|string|max:500',
        ]);

        $serviceRequest = VmServiceRequest::create([
            'vm_borrow_request_id' => $vmBorrow->id,
            'requester_id'         => $user->id,
            ...$data,
        ]);

        // Notify all admins
        $admins = User::where('role', 'admin')->get();
        Notification::send($admins, new ServiceRequestReceived($serviceRequest));

        return redirect()->route('vm_borrow.show', $vmBorrow)
            ->with('success', 'Permintaan layanan berhasil dikirim. Admin akan meninjau permintaan Anda.');
    }

    public function updateStatus(Request $request, VmBorrowRequest $vmBorrow, VmServiceRequest $serviceRequest)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        if ($serviceRequest->vm_borrow_request_id !== $vmBorrow->id) {
            abort(404);
        }

        $data = $request->validate([
            'status'         => 'required|in:pending,in_progress,completed,rejected',
            'admin_response' => 'required_if:status,completed,rejected|nullable|string|max:2000',
        ]);

        $update = ['status' => $data['status'], 'handler_id' => Auth::id()];

        if (in_array($data['status'], ['completed', 'rejected'])) {
            $update['admin_response'] = $data['admin_response'];
            $update['completed_at'] = now();
        }

        $serviceRequest->update($update);

        // Notify the requester (student) about the status change
        $serviceRequest->requester->notify(new ServiceRequestUpdated($serviceRequest));

        $message = match ($data['status']) {
            'completed' => 'Service request telah diselesaikan.',
            'rejected'  => 'Service request telah ditolak.',
            'in_progress' => 'Service request sedang diproses.',
            default     => 'Status service request diperbarui.',
        };

        return redirect()->route('vm_borrow.show', $vmBorrow)
            ->with('success', $message);
    }
}