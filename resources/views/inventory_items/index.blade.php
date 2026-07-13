<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-4">
            <h2 class="text-lg font-semibold text-neutral-800">
                {{ __('Data Barang & Alat') }}
            </h2>
            @if(Auth::user()->isAdmin())
                <a href="{{ route('inventory_items.create') }}" class="fluent-btn fluent-btn-primary fluent-btn-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Barang
                </a>
            @endif
        </div>
    </x-slot>

    <div class="space-y-6">
        @if(session('success'))
            <div class="px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium rounded-xl">
                {{ session('success') }}
            </div>
        @endif

        <!-- Filter -->
        <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm p-5">
            <form action="{{ route('inventory_items.index') }}" method="GET" class="flex flex-wrap gap-4 items-end">
                <div>
                    <x-input-label for="category" :value="__('Filter Kategori')" />
                    <select name="category" id="category" class="fluent-select text-sm min-w-[180px]">
                        <option value="">Semua Kategori</option>
                        @foreach($items->pluck('category')->filter()->unique() as $category)
                            <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>{{ $category }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="condition" :value="__('Filter Kondisi')" />
                    <select name="condition" id="condition" class="fluent-select text-sm min-w-[150px]">
                        <option value="">Semua Kondisi</option>
                        <option value="baik" {{ request('condition') === 'baik' ? 'selected' : '' }}>Baik</option>
                        <option value="rusak" {{ request('condition') === 'rusak' ? 'selected' : '' }}>Rusak</option>
                        <option value="perbaikan" {{ request('condition') === 'perbaikan' ? 'selected' : '' }}>Perbaikan</option>
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <x-primary-button type="submit" size="sm">Filter</x-primary-button>
                    <a href="{{ route('inventory_items.index') }}" class="fluent-btn fluent-btn-ghost fluent-btn-sm">Reset</a>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm overflow-hidden">
            <div class="overflow-x-auto">
            <table class="fluent-table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Stok</th>
                        <th>Kondisi</th>
                        <th>Lokasi</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td class="font-medium text-neutral-800">{{ $item->code }}</td>
                            <td class="font-medium text-neutral-800">{{ $item->name }}</td>
                            <td>{{ $item->category ?? '-' }}</td>
                            <td>{{ $item->available_stock }} / {{ $item->total_stock }} {{ $item->unit }}</td>
                            <td>{{ $item->condition }}</td>
                            <td>{{ $item->location ?? '-' }}</td>
                            <td>
                                @if($item->is_active)
                                    <span class="fluent-badge fluent-badge-success">Aktif</span>
                                @else
                                    <span class="fluent-badge fluent-badge-neutral">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('inventory_items.show', $item) }}" class="fluent-btn fluent-btn-ghost fluent-btn-sm">Detail</a>
                                    @if(Auth::user()->isAdmin())
                                        <form action="{{ route('inventory_items.destroy', $item) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="fluent-btn fluent-btn-danger fluent-btn-sm" onclick="return confirm('Hapus barang ini?')">Hapus</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-neutral-400">
                                <div class="flex flex-col items-center gap-2">
                                    <svg class="w-10 h-10 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                    <p class="text-sm font-medium">Belum ada data barang.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>

        <div class="mt-4">
            {{ $items->links() }}
        </div>
    </div>
</x-app-layout>
