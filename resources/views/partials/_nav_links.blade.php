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
