{{-- Navigation Links — Fluent Design --}}

{{-- Menu Utama --}}
<div class="sb-category-label">Menu Utama</div>

<x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
    <span class="sb-nav-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg></span>
    <span class="sb-nav-label">Dashboard</span>
</x-nav-link>

{{-- Informasi & Konten --}}
<div class="sb-category-label">Informasi & Konten</div>

<x-nav-link :href="route('bulletins.index')" :active="request()->routeIs('bulletins.*')">
    <span class="sb-nav-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg></span>
    <span class="sb-nav-label">Buletin</span>
</x-nav-link>

{{-- Lab & Inventaris --}}
<div class="sb-category-label">Lab & Inventaris</div>

<x-nav-link :href="route('usage_records.index')" :active="request()->routeIs('usage_records.*')">
    <span class="sb-nav-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg></span>
    <span class="sb-nav-label">Record Penggunaan</span>
</x-nav-link>

<x-nav-link :href="route('inventory_items.index')" :active="request()->routeIs('inventory_items.*')">
    <span class="sb-nav-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg></span>
    <span class="sb-nav-label">Barang & Alat</span>
</x-nav-link>

<x-nav-link :href="route('borrow_records.index')" :active="request()->routeIs('borrow_records.*')">
    <span class="sb-nav-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg></span>
    <span class="sb-nav-label">Peminjaman</span>
</x-nav-link>

<x-nav-link :href="route('vm_borrow.index')" :active="request()->routeIs('vm_borrow.*')">
    <span class="sb-nav-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg></span>
    <span class="sb-nav-label">VM</span>
</x-nav-link>

@if(Auth::check() && Auth::user()->isAdmin())
    @php
        $pendingVm = \App\Models\VmBorrowRequest::where('status', 'pending')->count();
        $pendingService = \App\Models\VmServiceRequest::where('status', 'pending')->count();
        $pendingReset = \App\Models\VmBorrowRequest::where('reset_status', 'pending')->count();
        $totalPending = $pendingVm + $pendingService + $pendingReset;
    @endphp
    @if($totalPending > 0)
    <div class="sb-pending-widget">
        <div class="sb-pending-header">
            <svg class="w-3 h-3 text-amber-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
            <span class="sb-pending-title">Pending</span>
            <span class="sb-pending-badge">{{ $totalPending }}</span>
        </div>
        <div class="sb-pending-items">
            @if($pendingVm > 0)
            <a href="{{ route('vm_borrow.index') }}" class="sb-pending-item">
                <span>VM Request</span>
                <span class="sb-pending-count">{{ $pendingVm }}</span>
            </a>
            @endif
            @if($pendingService > 0)
            <a href="{{ route('vm_borrow.index') }}" class="sb-pending-item">
                <span>Service Request</span>
                <span class="sb-pending-count">{{ $pendingService }}</span>
            </a>
            @endif
            @if($pendingReset > 0)
            <a href="{{ route('vm_borrow.index') }}" class="sb-pending-item">
                <span>Reset Request</span>
                <span class="sb-pending-count">{{ $pendingReset }}</span>
            </a>
            @endif
        </div>
    </div>
    @endif
@endif

@if(Auth::check() && !Auth::user()->isAdmin())
    @php
        $myVm = \App\Models\VmBorrowRequest::where('user_id', Auth::id())
            ->where(function ($q) {
                $q->whereIn('status', ['on-review', 'approved'])
                  ->orWhereIn('reset_status', ['pending', 'approved', 'rejected']);
            })
            ->count();
    @endphp
    @if($myVm > 0)
    <div class="sb-pending-widget">
        <div class="sb-pending-header">
            <svg class="w-3 h-3 text-blue-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
            <span class="sb-pending-title">VM Saya</span>
            <span class="sb-pending-badge sb-pending-badge-blue">{{ $myVm }}</span>
        </div>
        <a href="{{ route('vm_borrow.index') }}" class="sb-pending-item">
            <span>Lihat detail</span>
            <svg class="w-3 h-3 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>
    @endif
@endif

@if(Auth::check() && Auth::user()->isAdmin())
{{-- Administrasi --}}
<div class="sb-category-label">Administrasi</div>

<x-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
    <span class="sb-nav-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path></svg></span>
    <span class="sb-nav-label">User</span>
</x-nav-link>

<x-nav-link :href="route('vm_hosts.index')" :active="request()->routeIs('vm_hosts.*')">
    <span class="sb-nav-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path></svg></span>
    <span class="sb-nav-label">Host VM</span>
</x-nav-link>

<x-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')">
    <span class="sb-nav-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg></span>
    <span class="sb-nav-label">Laporan</span>
</x-nav-link>
@endif
