{{-- Navigation Links Partial --}}
<x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
    {{ __('Dashboard') }}
</x-nav-link>
<x-nav-link :href="route('bulletins.index')" :active="request()->routeIs('bulletins.*')">
    {{ __('Buletin') }}
</x-nav-link>
<x-nav-link :href="route('usage_records.index')" :active="request()->routeIs('usage_records.*')">
    {{ __('Record Penggunaan') }}
</x-nav-link>
<x-nav-link :href="route('inventory_items.index')" :active="request()->routeIs('inventory_items.*')">
    {{ __('Barang & Alat') }}
</x-nav-link>
<x-nav-link :href="route('borrow_records.index')" :active="request()->routeIs('borrow_records.*')">
    {{ __('Peminjaman') }}
</x-nav-link>
<x-nav-link :href="route('vm_borrow.index')" :active="request()->routeIs('vm_borrow.*')">
    {{ __('VM') }}
</x-nav-link>
@if(Auth::check() && Auth::user()->isAdmin())
<x-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')">
    {{ __('Laporan') }}
</x-nav-link>
@endif