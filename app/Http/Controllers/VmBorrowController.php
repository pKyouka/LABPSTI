<?php

namespace App\Http\Controllers;

use App\Models\VmBorrowRequest;
use App\Models\VmHost;
use App\Services\AnsibleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class VmBorrowController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            $requests = VmBorrowRequest::with('user')
                ->latest()
                ->paginate(20);
        } else {
            $requests = VmBorrowRequest::where('user_id', $user->id)
                ->latest()
                ->paginate(20);
        }

        return view('vm_borrow.index', compact('requests'));
    }

    public function create()
    {
        $osVersions = VmBorrowRequest::osVersions();
        return view('vm_borrow.create', compact('osVersions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'os_type'       => 'required|in:debian,ubuntu',
            'os_version'    => 'required|string',
            'purpose'       => 'required|string|max:500',
            'duration_days' => 'required|integer|min:1|max:365',
            'start_date'    => 'required|date|after_or_equal:today',
        ]);

        $osVersions = VmBorrowRequest::osVersions();
        if (!in_array($data['os_version'], $osVersions[$data['os_type']] ?? [])) {
            return back()->withErrors(['os_version' => 'Versi OS tidak valid untuk tipe yang dipilih.'])->withInput();
        }

        VmBorrowRequest::create([
            ...$data,
            'user_id' => Auth::id(),
            'status'  => 'pending',
        ]);

        return redirect()->route('vm_borrow.index')
            ->with('success', 'Permintaan peminjaman VM berhasil dikirim. Menunggu persetujuan admin.');
    }

    public function show(VmBorrowRequest $vmBorrow)
    {
        $this->ensureOwnerOrAdmin($vmBorrow);

        $hosts = VmHost::where('is_active', true)->orderBy('name')->get();

        return view('vm_borrow.show', compact('vmBorrow', 'hosts'));
    }

    private function ensureAdmin()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }
    }

    private function ensureOwnerOrAdmin(VmBorrowRequest $vmBorrow)
    {
        $user = Auth::user();
        if (!$user->isAdmin() && $vmBorrow->user_id !== $user->id) {
            abort(403);
        }
    }

    private function ensurePending(VmBorrowRequest $vmBorrow)
    {
        if ($vmBorrow->status !== 'pending' && $vmBorrow->status !== 'on-review') {
            return redirect()->route('vm_borrow.show', $vmBorrow)
                ->with('success', 'Permintaan ini sudah diproses sebelumnya.');
        }
        return null;
    }

    /**
     * ACC Tahap 1: Pindahkan status pending → on-review (pembuatan VM)
     */
    public function moveToReview(Request $request, VmBorrowRequest $vmBorrow)
    {
        $this->ensureAdmin();

        if ($vmBorrow->status !== 'pending') {
            return redirect()->route('vm_borrow.show', $vmBorrow)
                ->with('success', 'Permintaan ini sudah diproses sebelumnya.');
        }

        $data = $request->validate([
            'host_id' => 'nullable|exists:vm_hosts,id',
        ]);

        $vmBorrow->update([
            'status'      => 'on-review',
            'host_id'     => $data['host_id'] ?? null,
            'approved_by' => Auth::id(),
        ]);

        return redirect()->route('vm_borrow.show', $vmBorrow)
            ->with('success', 'Permintaan VM telah disetujui. Status berubah menjadi On-Review — silakan buat VM dan berikan kredensial setelah selesai.');
    }

    /**
     * ACC Tahap 2: on-review → approved (VM sudah jadi, berikan kredensial)
     */
    public function approve(Request $request, VmBorrowRequest $vmBorrow)
    {
        $this->ensureAdmin();

        if ($vmBorrow->status !== 'on-review') {
            return redirect()->route('vm_borrow.show', $vmBorrow)
                ->with('success', 'Permintaan ini sudah diproses atau belum dalam status on-review.');
        }

        $data = $request->validate([
            'host_id'       => 'nullable|exists:vm_hosts,id',
            'host_ip'       => 'required|string|max:255',
            'vm_username'   => 'required|string|max:255',
            'vm_password'   => 'required|string|max:255',
            'admin_notes'   => 'nullable|string|max:1000',
            'duration_days' => 'required|integer|min:1|max:365',
        ]);

        $vmBorrow->update([
            'status'        => 'approved',
            'host_id'       => $data['host_id'] ?? null,
            'host_ip'       => $data['host_ip'],
            'vm_username'   => $data['vm_username'],
            'vm_password'   => $data['vm_password'],
            'admin_notes'   => $data['admin_notes'],
            'duration_days' => $data['duration_days'],
            'end_date'      => \Carbon\Carbon::parse($vmBorrow->start_date)->addDays((int) $data['duration_days']),
            'approved_by'   => Auth::id(),
        ]);

        $message = 'VM telah berhasil diaktifkan. Kredensial telah diberikan kepada peminjam.';

        return redirect()->route('vm_borrow.show', $vmBorrow)
            ->with('success', $message);
    }

    public function reject(Request $request, VmBorrowRequest $vmBorrow)
    {
        $this->ensureAdmin();

        if ($redirect = $this->ensurePending($vmBorrow)) {
            return $redirect;
        }

        $request->validate([
            'admin_notes' => 'required|string|max:1000',
        ]);

        $vmBorrow->update([
            'status'      => 'rejected',
            'admin_notes' => $request->admin_notes,
            'approved_by' => Auth::id(),
        ]);

        return redirect()->route('vm_borrow.show', $vmBorrow)
            ->with('success', 'Permintaan VM telah ditolak.');
    }

    public function requestReset(Request $request, VmBorrowRequest $vmBorrow)
    {
        $this->ensureOwnerOrAdmin($vmBorrow);

        if ($vmBorrow->status !== 'approved') {
            return back()->with('error', 'Hanya VM yang disetujui yang dapat diminta reset.');
        }

        if ($vmBorrow->reset_status !== 'none' && $vmBorrow->reset_status !== 'rejected') {
            return back()->with('error', 'Reset sudah diminta atau diproses.');
        }

        $vmBorrow->update([
            'reset_status' => 'pending',
            'reset_reason' => $request->reset_reason,
            'reset_requested_at' => now(),
        ]);

        return back()->with('success', 'Permintaan reset telah dikirim ke admin.');
    }

    public function approveReset(Request $request, VmBorrowRequest $vmBorrow)
    {
        $this->ensureAdmin();

        if ($vmBorrow->reset_status !== 'pending') {
            return back()->with('error', 'Tidak ada permintaan reset pending untuk VM ini.');
        }

        $vmBorrow->update([
            'reset_status' => 'approved',
            'reset_approved_by' => Auth::id(),
            'reset_approved_at' => now(),
        ]);

        return back()->with('success', 'Reset telah disetujui.');
    }

    public function rejectReset(Request $request, VmBorrowRequest $vmBorrow)
    {
        $this->ensureAdmin();

        if ($vmBorrow->reset_status !== 'pending') {
            return back()->with('error', 'Tidak ada permintaan reset pending untuk VM ini.');
        }

        $request->validate([
            'reset_rejection_reason' => 'required|string|max:1000',
        ]);

        $vmBorrow->update([
            'reset_status' => 'rejected',
            'reset_rejected_by' => Auth::id(),
            'reset_rejected_at' => now(),
            'reset_rejection_reason' => $request->reset_rejection_reason,
        ]);

        return back()->with('success', 'Reset telah ditolak.');
    }
}
