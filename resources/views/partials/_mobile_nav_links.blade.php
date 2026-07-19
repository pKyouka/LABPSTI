{{-- Mobile Navigation Links --}}
<x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
    {{ __('Dashboard') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('bulletins.index')" :active="request()->routeIs('bulletins.*')">
    {{ __('Buletin') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('usage_records.index')" :active="request()->routeIs('usage_records.*')">
    {{ __('Record Penggunaan') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('inventory_items.index')" :active="request()->routeIs('inventory_items.*')">
    {{ __('Barang & Alat') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('borrow_records.index')" :active="request()->routeIs('borrow_records.*')">
    {{ __('Peminjaman') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('vm_borrow.index')" :active="request()->routeIs('vm_borrow.*')">
    {{ __('VM') }}
</x-responsive-nav-link>
@if(Auth::check() && Auth::user()->isAdmin())
<x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
    {{ __('User') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('proxmox.index')" :active="request()->routeIs('proxmox.*') || request()->routeIs('vm_hosts.*')">
    {{ __('Host & Proxmox') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')">
    {{ __('Laporan') }}
</x-responsive-nav-link>
@endif
