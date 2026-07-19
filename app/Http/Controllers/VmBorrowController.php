<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\VmBorrowRequest;
use App\Models\VmHost;
use App\Notifications\ResetRequestReceived;
use App\Notifications\ResetRequestUpdated;
use App\Notifications\VmBorrowRequestReceived;
use App\Notifications\VmBorrowRequestUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class VmBorrowController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $query = VmBorrowRequest::with('user')
            ->withCount(['serviceRequests as pending_service_count' => fn ($q) => $q->where('status', 'pending')]);

        if (! $user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        $requests = $query->latest()->paginate(20);

        return view('vm_borrow.index', compact('requests'));
    }

    public function create()
    {
        $osVersions = VmBorrowRequest::osVersions();
        $users = Auth::user()->isAdmin()
            ? User::orderBy('name')->get(['id', 'name', 'email', 'role'])
            : collect();

        return view('vm_borrow.create', compact('osVersions', 'users'));
    }

    public function store(Request $request)
    {
        $isAdmin = Auth::user()->isAdmin();

        $rules = [
            'os_type'       => 'required|in:debian,ubuntu',
            'os_version'    => 'required|string',
            'purpose'       => 'required|string|max:500',
            'duration_days' => 'required|integer|min:1|max:365',
            'start_date'    => 'required|date|after_or_equal:today',
        ];

        if ($isAdmin) {
            $rules['user_id'] = 'required|exists:users,id';
        }

        $data = $request->validate($rules);

        $osVersions = VmBorrowRequest::osVersions();
        if (! in_array($data['os_version'], $osVersions[$data['os_type']] ?? [])) {
            return back()->withErrors(['os_version' => 'Versi OS tidak valid untuk tipe yang dipilih.'])->withInput();
        }

        $ownerId = $isAdmin ? (int) $data['user_id'] : Auth::id();
        unset($data['user_id']);

        $vmRequest = VmBorrowRequest::create([
            ...$data,
            'user_id' => $ownerId,
            'status'  => 'pending',
        ]);

        if ($isAdmin) {
            // Admin buat atas nama user → langsung ke detail untuk ACC
            return redirect()->route('vm_borrow.show', $vmRequest)
                ->with('success', 'Permintaan VM dibuat untuk '.$vmRequest->user->display_name.'. Lanjutkan ACC di halaman ini.');
        }

        // User biasa → notif admin
        $admins = User::where('role', 'admin')->get();
        Notification::send($admins, new VmBorrowRequestReceived($vmRequest));

        return redirect()->route('vm_borrow.index')
            ->with('success', 'Permintaan peminjaman VM berhasil dikirim. Menunggu persetujuan admin.');
    }

    public function show(VmBorrowRequest $vmBorrow)
    {
        $this->ensureOwnerOrAdmin($vmBorrow);

        $hosts = VmHost::where('is_active', true)->orderBy('name')->get();

        $vmBorrow->load(['serviceRequests' => fn($q) => $q->latest()]);

        $hostSpecs = $hosts->mapWithKeys(fn ($h) => [
            $h->id => [
                'name'          => $h->name,
                'ip_address'    => $h->ip_address,
                'cpu_total'     => $h->cpu_total,
                'cpu_used'      => $h->cpu_used,
                'ram_total'     => $h->ram_total,
                'ram_used'      => $h->ram_used,
                'storage_total' => $h->storage_total,
                'storage_used'  => $h->storage_used,
                'is_active'     => $h->is_active,
                'status'        => $h->statusLabel(),
                'notes'         => $h->notes,
            ],
        ])->all();

        $hostsData = $hosts->mapWithKeys(fn ($h) => [
            $h->id => ['ip' => $h->ip_address, 'full' => $h->isFull()],
        ])->all();

        return view('vm_borrow.show', compact('vmBorrow', 'hosts', 'hostSpecs', 'hostsData'));
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
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

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

        // Notify the student
        $vmBorrow->user->notify(new VmBorrowRequestUpdated($vmBorrow));

        return redirect()->route('vm_borrow.show', $vmBorrow)
            ->with('success', 'Permintaan VM telah disetujui. Status berubah menjadi On-Review — silakan buat VM dan berikan kredensial setelah selesai.');
    }

    /**
     * ACC Tahap 2: on-review → approved (VM sudah jadi, berikan kredensial)
     */
    public function approve(Request $request, VmBorrowRequest $vmBorrow)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        if ($vmBorrow->status !== 'on-review') {
            return redirect()->route('vm_borrow.show', $vmBorrow)
                ->with('success', 'Permintaan ini sudah diproses atau belum dalam status on-review.');
        }

        $data = $request->validate([
            'host_id'          => 'nullable|exists:vm_hosts,id',
            'host_ip'          => 'required|string|max:255',
            'vm_username'      => 'required|string|max:255',
            'vm_password'      => 'required|string|max:255',
            'admin_notes'      => 'nullable|string|max:1000',
            'duration_days'    => 'required|integer|min:1|max:365',
            'allocated_cpu'    => 'required|integer|min:1|max:128',
            'allocated_ram'    => 'required|integer|min:256|max:524288',
            'allocated_storage'=> 'required|integer|min:1|max:10000',
        ]);

        // Validasi resource tidak melebihi kapasitas host
        $host = $data['host_id'] ? VmHost::find($data['host_id']) : null;
        if ($host) {
            $cpu    = (int) $data['allocated_cpu'];
            $ram    = (int) $data['allocated_ram'];
            $storage= (int) $data['allocated_storage'];

            if ($cpu > $host->cpuAvailable()) {
                return back()->withErrors(['allocated_cpu' => "CPU tidak cukup. Tersedia hanya {$host->cpuAvailable()} cores."])->withInput();
            }
            if ($ram > $host->ramAvailable()) {
                return back()->withErrors(['allocated_ram' => "RAM tidak cukup. Tersedia hanya " . round($host->ramAvailable() / 1024, 1) . " GB."])->withInput();
            }
            if ($storage > $host->storageAvailable()) {
                return back()->withErrors(['allocated_storage' => "Storage tidak cukup. Tersedia hanya {$host->storageAvailable()} GB."])->withInput();
            }

            $host->deductResources($cpu, $ram, $storage);
        }

        $vmBorrow->update([
            'status'           => 'approved',
            'host_id'          => $data['host_id'] ?? null,
            'host_ip'          => $data['host_ip'],
            'allocated_cpu'    => (int) $data['allocated_cpu'],
            'allocated_ram'    => (int) $data['allocated_ram'],
            'allocated_storage'=> (int) $data['allocated_storage'],
            'vm_username'      => $data['vm_username'],
            'vm_password'      => $data['vm_password'],
            'admin_notes'      => $data['admin_notes'],
            'duration_days'    => $data['duration_days'],
            'end_date'         => \Carbon\Carbon::parse($vmBorrow->start_date)->addDays((int) $data['duration_days']),
            'approved_by'      => Auth::id(),
        ]);

        $hostName = $host ? $host->name : 'Host';
        $message = "VM telah berhasil diaktifkan di {$hostName}. Kredensial telah diberikan kepada peminjam.";

        // Notify the student
        $vmBorrow->user->notify(new VmBorrowRequestUpdated($vmBorrow));

        return redirect()->route('vm_borrow.show', $vmBorrow)
            ->with('success', $message);
    }

    /**
     * Edit resource alokasi VM yang sudah approved (termasuk pindah host).
     */
    public function editResources(Request $request, VmBorrowRequest $vmBorrow)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        if ($vmBorrow->status !== 'approved') {
            return redirect()->route('vm_borrow.show', $vmBorrow)
                ->with('success', 'Hanya VM yang sudah disetujui yang dapat diedit resourcenya.');
        }

        $data = $request->validate([
            'host_id'          => 'nullable|exists:vm_hosts,id',
            'host_ip'          => 'required|string|max:255',
            'allocated_cpu'    => 'required|integer|min:1|max:128',
            'allocated_ram'    => 'required|integer|min:256|max:524288',
            'allocated_storage'=> 'required|integer|min:1|max:10000',
        ]);

        $oldHost = $vmBorrow->host;
        $newHostId = $data['host_id'] ?? null;

        $oldCpu     = (int) ($vmBorrow->allocated_cpu ?? 0);
        $oldRam     = (int) ($vmBorrow->allocated_ram ?? 0);
        $oldStorage = (int) ($vmBorrow->allocated_storage ?? 0);
        $newCpu     = (int) $data['allocated_cpu'];
        $newRam     = (int) $data['allocated_ram'];
        $newStorage = (int) $data['allocated_storage'];

        // Fallback: jika user tidak pilih host baru, gunakan host yang sama
        if (!$newHostId && $oldHost) {
            $newHostId = $oldHost->id;
        }

        $newHost = $newHostId ? VmHost::find($newHostId) : null;

        // Jika host tidak berubah, cukup sesuaikan selisih resource
        if ($oldHost && $newHost && $oldHost->id === $newHost->id) {
            $cpuDiff = $newCpu - $oldCpu;
            $ramDiff = $newRam - $oldRam;
            $storageDiff = $newStorage - $oldStorage;

            // Validasi jika resource ditambah melebihi kapasitas
            if ($cpuDiff > 0 && $cpuDiff > $newHost->cpuAvailable()) {
                return back()->withErrors(['allocated_cpu' => "CPU tidak cukup. Tersedia hanya {$newHost->cpuAvailable()} cores (dibutuhkan tambahan {$cpuDiff})."])->withInput();
            }
            if ($ramDiff > 0 && $ramDiff > $newHost->ramAvailable()) {
                return back()->withErrors(['allocated_ram' => "RAM tidak cukup. Tersedia hanya " . round($newHost->ramAvailable() / 1024, 1) . " GB (dibutuhkan tambahan " . round($ramDiff / 1024, 1) . " GB)."])->withInput();
            }
            if ($storageDiff > 0 && $storageDiff > $newHost->storageAvailable()) {
                return back()->withErrors(['allocated_storage' => "Storage tidak cukup. Tersedia hanya {$newHost->storageAvailable()} GB (dibutuhkan tambahan {$storageDiff} GB)."])->withInput();
            }

            // Jika resource bertambah, potong dari host
            if ($cpuDiff > 0) {
                $newHost->deductResources($cpuDiff, 0, 0);
            }
            // Jika resource berkurang, kembalikan ke host
            if ($cpuDiff < 0) {
                $newHost->restoreResources(abs($cpuDiff), 0, 0);
            }

            if ($ramDiff > 0) {
                $newHost->deductResources(0, $ramDiff, 0);
            }
            if ($ramDiff < 0) {
                $newHost->restoreResources(0, abs($ramDiff), 0);
            }

            if ($storageDiff > 0) {
                $newHost->deductResources(0, 0, $storageDiff);
            }
            if ($storageDiff < 0) {
                $newHost->restoreResources(0, 0, abs($storageDiff));
            }
        }
        // Host berbeda: kembalikan ke host lama, validasi + potong host baru
        elseif ($newHost) {
            if ($oldHost) {
                $oldHost->restoreResources($oldCpu, $oldRam, $oldStorage);
            }

            if ($newCpu > $newHost->cpuAvailable()) {
                if ($oldHost) $oldHost->deductResources($oldCpu, $oldRam, $oldStorage);
                return back()->withErrors(['allocated_cpu' => "CPU tidak cukup. Tersedia hanya {$newHost->cpuAvailable()} cores."])->withInput();
            }
            if ($newRam > $newHost->ramAvailable()) {
                if ($oldHost) $oldHost->deductResources($oldCpu, $oldRam, $oldStorage);
                return back()->withErrors(['allocated_ram' => "RAM tidak cukup. Tersedia hanya " . round($newHost->ramAvailable() / 1024, 1) . " GB."])->withInput();
            }
            if ($newStorage > $newHost->storageAvailable()) {
                if ($oldHost) $oldHost->deductResources($oldCpu, $oldRam, $oldStorage);
                return back()->withErrors(['allocated_storage' => "Storage tidak cukup. Tersedia hanya {$newHost->storageAvailable()} GB."])->withInput();
            }

            $newHost->deductResources($newCpu, $newRam, $newStorage);
        }

        $vmBorrow->update([
            'host_id'          => $newHostId,
            'host_ip'          => $data['host_ip'],
            'allocated_cpu'    => $newCpu,
            'allocated_ram'    => $newRam,
            'allocated_storage'=> $newStorage,
        ]);

        $hostName = $newHost ? $newHost->name : ($oldHost ? $oldHost->name : 'Host');
        return redirect()->route('vm_borrow.show', $vmBorrow)
            ->with('success', "Resource VM di {$hostName} berhasil diperbarui.");
    }

    public function reject(Request $request, VmBorrowRequest $vmBorrow)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

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

        // Notify the student
        $vmBorrow->user->notify(new VmBorrowRequestUpdated($vmBorrow));

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

        $data = $request->validate([
            'reset_reason' => 'nullable|string|max:1000',
        ]);

        $vmBorrow->update([
            'reset_status' => 'pending',
            'reset_reason' => $data['reset_reason'] ?? null,
            'reset_requested_at' => now(),
        ]);

        // Notify all admins
        $admins = \App\Models\User::where('role', 'admin')->get();
        Notification::send($admins, new ResetRequestReceived($vmBorrow));

        return back()->with('success', 'Permintaan reset telah dikirim ke admin.');
    }

    public function approveReset(Request $request, VmBorrowRequest $vmBorrow)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        if ($vmBorrow->reset_status !== 'pending') {
            return back()->with('error', 'Tidak ada permintaan reset pending untuk VM ini.');
        }

        $vmBorrow->update([
            'reset_status' => 'approved',
            'reset_approved_by' => Auth::id(),
            'reset_approved_at' => now(),
        ]);

        // Notify the student
        $vmBorrow->user->notify(new ResetRequestUpdated($vmBorrow));

        return back()->with('success', 'Reset telah disetujui.');
    }

    public function rejectReset(Request $request, VmBorrowRequest $vmBorrow)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

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

        // Notify the student
        $vmBorrow->user->notify(new ResetRequestUpdated($vmBorrow));

        return back()->with('success', 'Reset telah ditolak.');
    }

    /**
     * Hapus permintaan VM.
     * Admin: semua status. User: hanya milik sendiri + status pending.
     * Restore resource host bila approved + ada alokasi.
     */
    public function destroy(VmBorrowRequest $vmBorrow)
    {
        $user = Auth::user();
        $isAdmin = $user->isAdmin();

        if (! $isAdmin && $vmBorrow->user_id !== $user->id) {
            abort(403);
        }

        if (! $isAdmin && $vmBorrow->status !== 'pending') {
            return back()->with('error', 'Hanya permintaan berstatus Menunggu yang bisa dihapus sendiri.');
        }

        // Kembalikan resource ke host bila sudah di-allocate
        if ($vmBorrow->status === 'approved' && $vmBorrow->host_id) {
            $host = $vmBorrow->host;
            if ($host) {
                $host->restoreResources(
                    (int) ($vmBorrow->allocated_cpu ?? 0),
                    (int) ($vmBorrow->allocated_ram ?? 0),
                    (int) ($vmBorrow->allocated_storage ?? 0),
                );
            }
        }

        $label = '#'.$vmBorrow->id.' · '.ucfirst($vmBorrow->os_type).' '.$vmBorrow->os_version;
        $vmBorrow->delete(); // service_requests cascade

        return redirect()
            ->route('vm_borrow.index')
            ->with('success', "Permintaan VM {$label} dihapus.");
    }
}
