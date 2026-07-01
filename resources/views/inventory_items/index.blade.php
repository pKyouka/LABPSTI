<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-4">
            <h2 class="font-bold font-bold text-2xl text-slate-900 leading-tight">
                {{ __('Data Barang & Alat') }}
            </h2>
            @if(Auth::user()->isAdmin())
                <a href="{{ route('inventory_items.create') }}" class="inline-flex items-center px-6 py-2.5 bg-cyan-600 text-white font-black rounded-xl hover:bg-cyan-700 transition shadow-lg shadow-cyan-200 uppercase tracking-widest text-[10px]">
                    Tambah Barang
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="panel-shell">
            @if(session('success'))
                <div class="panel-card mb-6">
                    <div class="panel-card-body">
                        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl" role="alert">
                            {{ session('success') }}
                        </div>
                    </div>
                </div>
            @endif

            <div class="panel-card overflow-hidden mb-6">
                <div class="panel-card-body">
                    <form action="{{ route('inventory_items.index') }}" method="GET" class="flex flex-wrap gap-4 items-end">
                        <div>
                            <x-input-label for="category" :value="__('Filter Kategori')" />
                            <select name="category" id="category" class="panel-select text-sm min-w-48">
                                <option value="">Semua Kategori</option>
                                @foreach($items->pluck('category')->filter()->unique() as $category)
                                    <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>{{ $category }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label for="condition" :value="__('Filter Kondisi')" />
                            <select name="condition" id="condition" class="panel-select text-sm min-w-40">
                                <option value="">Semua Kondisi</option>
                                <option value="baik" {{ request('condition') === 'baik' ? 'selected' : '' }}>Baik</option>
                                <option value="rusak" {{ request('condition') === 'rusak' ? 'selected' : '' }}>Rusak</option>
                                <option value="perbaikan" {{ request('condition') === 'perbaikan' ? 'selected' : '' }}>Perbaikan</option>
                            </select>
                        </div>
                        <div>
                            <x-primary-button type="submit">Filter</x-primary-button>
                            <a href="{{ route('inventory_items.index') }}" class="ml-2 text-sm text-slate-500 hover:text-slate-800 font-bold">Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="panel-card overflow-hidden">
                <div class="panel-card-body">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="table-head">
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
                            <tbody class="bg-white divide-y divide-slate-100">
                                @forelse($items as $item)
                                    <tr class="table-row hover:bg-slate-50/60 transition-colors">
                                        <td class="whitespace-nowrap font-semibold text-slate-900">{{ $item->code }}</td>
                                        <td class="font-semibold text-slate-900">{{ $item->name }}</td>
                                        <td>{{ $item->category ?? '-' }}</td>
                                        <td class="whitespace-nowrap">{{ $item->available_stock }} / {{ $item->total_stock }} {{ $item->unit }}</td>
                                        <td class="whitespace-nowrap">{{ $item->condition }}</td>
                                        <td class="whitespace-nowrap">{{ $item->location ?? '-' }}</td>
                                        <td class="whitespace-nowrap">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-widest {{ $item->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                                {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex items-center justify-end gap-4">
                                                <a href="{{ route('inventory_items.show', $item) }}" class="text-cyan-700 hover:text-cyan-900 font-black">Detail</a>
                                                @if(Auth::user()->isAdmin())
                                                    <form action="{{ route('inventory_items.destroy', $item) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-rose-600 hover:text-rose-800 font-black" onclick="return confirm('Hapus barang ini?')">Hapus</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-8 text-center text-slate-500">
                                            Belum ada data barang.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $items->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
