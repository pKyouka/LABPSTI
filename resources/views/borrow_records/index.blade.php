<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-4">
            <h2 class="font-display font-bold text-2xl text-slate-900 leading-tight">
                {{ __('Peminjaman Alat & Barang') }}
            </h2>
            <a href="{{ route('borrow_records.create') }}" class="inline-flex items-center px-6 py-2.5 bg-cyan-600 text-white font-black rounded-xl hover:bg-cyan-700 transition shadow-lg shadow-cyan-200 uppercase tracking-widest text-[10px]">
                Ajukan Peminjaman
            </a>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="panel-shell">
            <div class="panel-card overflow-hidden mb-6">
                <div class="panel-card-body">
                    <form action="{{ route('borrow_records.index') }}" method="GET" class="flex flex-wrap gap-4 items-end">
                        <div>
                            <x-input-label for="inventory_item_id" :value="__('Filter Barang')" />
                            <select name="inventory_item_id" id="inventory_item_id" class="panel-select text-sm min-w-56">
                                <option value="">Semua Barang</option>
                                @foreach($items as $item)
                                    <option value="{{ $item->id }}" {{ request('inventory_item_id') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label for="status" :value="__('Filter Status')" />
                            <select name="status" id="status" class="panel-select text-sm min-w-44">
                                <option value="">Semua Status</option>
                                <option value="menunggu" {{ request('status') === 'menunggu' ? 'selected' : '' }}>Menunggu ACC</option>
                                <option value="dipinjam" {{ request('status') === 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                                <option value="dikembalikan" {{ request('status') === 'dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                                <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                        </div>
                        <div>
                            <x-input-label for="date" :value="__('Filter Tanggal')" />
                            <x-text-input id="date" type="date" name="date" class="panel-input text-sm" :value="request('date')" />
                        </div>
                        <div>
                            <x-primary-button type="submit">Filter</x-primary-button>
                            <a href="{{ route('borrow_records.index') }}" class="ml-2 text-sm text-slate-500 hover:text-slate-800 font-bold">Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="panel-card overflow-hidden">
                <div class="panel-card-body">
                    @if(session('success'))
                        <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl relative" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="table-head">
                                <tr>
                                    <th>Nama</th>
                                    <th>NIM/ID</th>
                                    <th>Barang</th>
                                    <th>Jumlah</th>
                                    <th>Dipinjam</th>
                                    <th>Jatuh Tempo</th>
                                    <th>Status ACC</th>
                                    <th>Status</th>
                                    <th class="text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-100">
                                @forelse($records as $record)
                                    <tr class="table-row hover:bg-slate-50/60 transition-colors">
                                        <td class="whitespace-nowrap font-semibold text-slate-900">{{ $record->user_name }}</td>
                                        <td class="whitespace-nowrap">{{ $record->user_id_number }}</td>
                                        <td class="whitespace-nowrap">{{ $record->inventoryItem->name }}</td>
                                        <td class="whitespace-nowrap">{{ $record->quantity }}</td>
                                        <td class="whitespace-nowrap">{{ $record->borrowed_at->format('d/m/Y H:i') }}</td>
                                        <td class="whitespace-nowrap">{{ $record->due_at?->format('d/m/Y H:i') ?? '-' }}</td>
                                        <td class="whitespace-nowrap">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-widest {{ $record->approval_status === 'approved' ? 'bg-emerald-50 text-emerald-700' : ($record->approval_status === 'rejected' ? 'bg-rose-50 text-rose-700' : 'bg-amber-50 text-amber-700') }}">
                                                {{ $record->approval_status }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-widest {{ $record->status === 'dikembalikan' ? 'bg-emerald-50 text-emerald-700' : ($record->status === 'ditolak' ? 'bg-rose-50 text-rose-700' : 'bg-amber-50 text-amber-700') }}">
                                                {{ $record->status }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap text-right text-sm font-medium">
                                            @if(auth()->user()->isAdmin() && $record->approval_status === 'pending')
                                                <form action="{{ route('borrow_records.approve', $record) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-emerald-600 hover:text-emerald-800 font-black">ACC</button>
                                                </form>
                                                <form action="{{ route('borrow_records.reject', $record) }}" method="POST" class="inline ml-3">
                                                    @csrf
                                                    <button type="submit" class="text-rose-600 hover:text-rose-800 font-black" onclick="return confirm('Tolak peminjaman ini?')">Tolak</button>
                                                </form>
                                            @elseif($record->approval_status === 'approved' && $record->returned_at === null)
                                                <form action="{{ route('borrow_records.return', $record) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-cyan-700 hover:text-cyan-900 font-black" onclick="return confirm('Tandai barang sudah dikembalikan?')">Kembalikan</button>
                                                </form>
                                            @else
                                                <span class="text-slate-400">Selesai</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="px-6 py-8 whitespace-nowrap text-center text-slate-500">
                                            Belum ada data peminjaman.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $records->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
