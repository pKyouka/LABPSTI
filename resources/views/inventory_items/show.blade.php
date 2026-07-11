<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-start gap-4">
            <div>
                <h2 class="text-lg font-semibold text-neutral-800">{{ __('Detail Barang / Alat') }}</h2>
                <p class="text-sm text-neutral-500 mt-1">Informasi lengkap barang inventaris.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('inventory_items.index') }}" class="fluent-btn fluent-btn-ghost fluent-btn-sm">Kembali</a>
                @if(Auth::user()->isAdmin() && $item->borrow_records_count === 0)
                    <form action="{{ route('inventory_items.destroy', $item) }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="fluent-btn fluent-btn-danger fluent-btn-sm" onclick="return confirm('Hapus barang ini?')">Hapus</button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if(session('error'))
            <div class="px-4 py-3 bg-red-50 border border-red-200 text-red-700 text-sm font-medium rounded-xl">{{ session('error') }}</div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm p-6 lg:col-span-2 space-y-5">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-neutral-400 mb-1">Kode Barang</p>
                    <p class="text-2xl font-bold text-neutral-800">{{ $item->code }}</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><p class="text-xs font-semibold uppercase tracking-wider text-neutral-400 mb-1">Nama Barang</p><p class="text-base font-semibold text-neutral-800">{{ $item->name }}</p></div>
                    <div><p class="text-xs font-semibold uppercase tracking-wider text-neutral-400 mb-1">Kategori</p><p class="text-base font-semibold text-neutral-800">{{ $item->category ?? '-' }}</p></div>
                    <div><p class="text-xs font-semibold uppercase tracking-wider text-neutral-400 mb-1">Satuan</p><p class="text-base font-semibold text-neutral-800">{{ $item->unit }}</p></div>
                    <div><p class="text-xs font-semibold uppercase tracking-wider text-neutral-400 mb-1">Lokasi</p><p class="text-base font-semibold text-neutral-800">{{ $item->location ?? '-' }}</p></div>
                    <div><p class="text-xs font-semibold uppercase tracking-wider text-neutral-400 mb-1">Kondisi</p><p class="text-base font-semibold text-neutral-800">{{ $item->condition }}</p></div>
                    <div><p class="text-xs font-semibold uppercase tracking-wider text-neutral-400 mb-1">Status</p><span class="fluent-badge {{ $item->is_active ? 'fluent-badge-success' : 'fluent-badge-neutral' }}">{{ $item->is_active ? 'Aktif' : 'Nonaktif' }}</span></div>
                </div>
                @if($item->description)
                    <div><p class="text-xs font-semibold uppercase tracking-wider text-neutral-400 mb-1">Deskripsi</p><p class="text-sm text-neutral-600">{{ $item->description }}</p></div>
                @endif
            </div>

            <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm p-6">
                <p class="text-xs font-semibold uppercase tracking-wider text-neutral-400 mb-4">Ringkasan Stok</p>
                <div class="space-y-4">
                    <div><p class="text-sm text-neutral-500 mb-1">Tersedia</p><p class="text-2xl font-bold text-accent">{{ $item->available_stock }}</p></div>
                    <div><p class="text-sm text-neutral-500 mb-1">Total</p><p class="text-2xl font-bold text-neutral-800">{{ $item->total_stock }}</p></div>
                    <div><p class="text-sm text-neutral-500 mb-1">Riwayat Peminjaman</p><p class="text-2xl font-bold text-emerald-600">{{ $item->borrow_records_count }}</p></div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>