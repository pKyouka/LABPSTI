<?php

namespace App\Http\Controllers;

use App\Models\VmHost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VmHostController extends Controller
{
    /**
     * Display a listing of all hosts (public — no login required)
     */
    public function publicIndex()
    {
        $hosts = VmHost::orderBy('name')->get();
        return view('vm_hosts.public-index', compact('hosts'));
    }

    /**
     * Display listing for admin management
     */
    public function index()
    {
        $this->ensureAdmin();

        $hosts = VmHost::orderBy('name')->paginate(20);
        return view('vm_hosts.index', compact('hosts'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $this->ensureAdmin();
        return view('vm_hosts.create');
    }

    /**
     * Store a new host
     */
    public function store(Request $request)
    {
        $this->ensureAdmin();

        $data = $request->validate([
            'name'          => 'required|string|max:255|unique:vm_hosts,name',
            'ip_address'    => 'nullable|string|max:255',
            'cpu_total'     => 'required|integer|min:0',
            'cpu_used'      => 'required|integer|min:0|lte:cpu_total',
            'ram_total'     => 'required|integer|min:0',
            'ram_used'      => 'required|integer|min:0|lte:ram_total',
            'storage_total' => 'required|integer|min:0',
            'storage_used'  => 'required|integer|min:0|lte:storage_total',
            'is_active'     => 'boolean',
            'notes'         => 'nullable|string|max:2000',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        VmHost::create($data);

        return redirect()->route('vm_hosts.index')
            ->with('success', 'Host berhasil ditambahkan.');
    }

    /**
     * Show edit form
     */
    public function edit(VmHost $vmHost)
    {
        $this->ensureAdmin();
        return view('vm_hosts.edit', compact('vmHost'));
    }

    /**
     * Update a host
     */
    public function update(Request $request, VmHost $vmHost)
    {
        $this->ensureAdmin();

        $data = $request->validate([
            'name'          => 'required|string|max:255|unique:vm_hosts,name,' . $vmHost->id,
            'ip_address'    => 'nullable|string|max:255',
            'cpu_total'     => 'required|integer|min:0',
            'cpu_used'      => 'required|integer|min:0|lte:cpu_total',
            'ram_total'     => 'required|integer|min:0',
            'ram_used'      => 'required|integer|min:0|lte:ram_total',
            'storage_total' => 'required|integer|min:0',
            'storage_used'  => 'required|integer|min:0|lte:storage_total',
            'is_active'     => 'boolean',
            'notes'         => 'nullable|string|max:2000',
        ]);

        $data['is_active'] = $request->boolean('is_active', false);

        $vmHost->update($data);

        return redirect()->route('vm_hosts.index')
            ->with('success', 'Host berhasil diperbarui.');
    }

    /**
     * Delete a host
     */
    public function destroy(VmHost $vmHost)
    {
        $this->ensureAdmin();

        // Check if host has active borrow requests
        $activeRequests = $vmHost->vmBorrowRequests()
            ->whereIn('status', ['approved', 'on-review'])
            ->count();

        if ($activeRequests > 0) {
            return back()->with('error', 'Tidak dapat menghapus host yang masih memiliki VM aktif atau dalam review.');
        }

        $vmHost->delete();

        return redirect()->route('vm_hosts.index')
            ->with('success', 'Host berhasil dihapus.');
    }

    private function ensureAdmin()
    {
        if (!Auth::user() || !Auth::user()->isAdmin()) {
            abort(403);
        }
    }
}