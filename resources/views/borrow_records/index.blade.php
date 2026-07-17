<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-4">
            <h2 class="text-lg font-semibold text-neutral-800">
                {{ __('Peminjaman Alat & Barang') }}
            </h2>
            <a href="{{ route('borrow_records.create') }}" class="fluent-btn fluent-btn-primary fluent-btn-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                Ajukan Peminjaman
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if(session('success'))
            <div class="px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium rounded-xl">{{ session('success') }}</div>
        @endif

        <!-- Filter -->
        <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm p-5">
            <form action="{{ route('borrow_records.index') }}" method="GET" class="flex flex-wrap gap-4 items-end">
                <div>
                    <x-input-label for="inventory_item_id" :value="__('Filter Barang')" />
                    <select name="inventory_item_id" id="inventory_item_id" class="fluent-select text-sm min-w-[200px]">
                        <option value="">Semua Barang</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}" {{ request('inventory_item_id') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="status" :value="__('Filter Status')" />
                    <select name="status" id="status" class="fluent-select text-sm min-w-[160px]">
                        <option value="">Semua Status</option>
                        <option value="menunggu" {{ request('status') === 'menunggu' ? 'selected' : '' }}>Menunggu ACC</option>
                        <option value="dipinjam" {{ request('status') === 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                        <option value="dikembalikan" {{ request('status') === 'dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                        <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                <div>
                    <x-input-label for="date" :value="__('Filter Tanggal')" />
                    <x-text-input id="date" type="date" name="date" :value="request('date')" />
                </div>
                <div class="flex items-center gap-2">
                    <x-primary-button type="submit" size="sm">Filter</x-primary-button>
                    <a href="{{ route('borrow_records.index') }}" class="fluent-btn fluent-btn-ghost fluent-btn-sm">Reset</a>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm overflow-x-auto">
            <table class="fluent-table min-w-[1400px]">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>NIM/ID</th>
                        <th>Barang</th>
                        <th class="text-center">Jml</th>
                        <th>Dipinjam</th>
                        <th>Jatuh Tempo</th>
                        <th>PDF</th>
                        <th>PDF ACC</th>
                        <th>Validasi</th>
                        <th>Ready</th>
                        <th>Diambil</th>
                        <th>Status ACC</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $record)
                        <tr>
                            <td class="font-medium text-neutral-800">{{ $record->user_name }}</td>
                            <td>{{ $record->user_id_number }}</td>
                            <td class="font-medium">{{ $record->inventoryItem->name }}</td>
                            <td class="text-center">{{ $record->quantity }}</td>
                            <td>{{ $record->borrowed_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $record->due_at?->format('d/m/Y H:i') ?? '-' }}</td>
                            <td>
                                @if($record->request_pdf_path)
                                    <a href="{{ route('borrow_records.pdf.request', $record) }}" target="_blank" class="text-accent font-medium text-sm hover:underline">Lihat</a>
                                @else
                                    <span class="text-neutral-400">-</span>
                                @endif
                            </td>
                            <td>
                                @if($record->approval_pdf_path)
                                    <a href="{{ route('borrow_records.pdf.approval', $record) }}" target="_blank" class="text-emerald-600 font-medium text-sm hover:underline">ACC</a>
                                @else
                                    <span class="text-neutral-400">-</span>
                                @endif
                            </td>
                            <td>
                                @if($record->approval_pdf_path)
                                    @if($record->approvalPdfIsValid())
                                        <span class="fluent-badge fluent-badge-success">Valid</span>
                                    @else
                                        <span class="fluent-badge fluent-badge-danger">Tidak Valid</span>
                                    @endif
                                @else
                                    <span class="text-neutral-400">-</span>
                                @endif
                            </td>
                            <td>{{ $record->ready_at?->format('d/m/Y H:i') ?? '-' }}</td>
                            <td>{{ $record->pickup_at?->format('d/m/Y H:i') ?? '-' }}</td>
                            <td>
                                @if($record->approval_status === 'approved')
                                    <span class="fluent-badge fluent-badge-success">Approved</span>
                                @elseif($record->approval_status === 'rejected')
                                    <span class="fluent-badge fluent-badge-danger">Rejected</span>
                                @else
                                    <span class="fluent-badge fluent-badge-warning">Pending</span>
                                @endif
                            </td>
                            <td>
                                @if($record->status === 'dikembalikan')
                                    <span class="fluent-badge fluent-badge-success">Dikembalikan</span>
                                @elseif($record->status === 'ditolak')
                                    <span class="fluent-badge fluent-badge-danger">Ditolak</span>
                                @else
                                    <span class="fluent-badge fluent-badge-warning">{{ $record->status }}</span>
                                @endif
                            </td>
                            <td class="text-right">
                                @if(auth()->user()->isAdmin() && $record->approval_status === 'pending')
                                    <div class="flex items-center justify-end gap-1 flex-wrap">
                                        <form action="{{ route('borrow_records.approve', $record) }}" method="POST" class="flex items-end gap-2 flex-wrap">
                                            @csrf
                                            <input type="datetime-local" name="ready_at" required class="fluent-input text-xs w-36" placeholder="Ready">
                                            <input type="datetime-local" name="pickup_at" required class="fluent-input text-xs w-36" placeholder="Pickup">
                                            <button type="submit" class="fluent-btn fluent-btn-primary fluent-btn-sm">ACC</button>
                                        </form>
                                        <form action="{{ route('borrow_records.reject', $record) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="fluent-btn fluent-btn-danger fluent-btn-sm" onclick="return confirm('Tolak peminjaman ini?')">Tolak</button>
                                        </form>
                                    </div>
                                @elseif($record->approval_status === 'approved' && $record->returned_at === null)
                                    <form action="{{ route('borrow_records.return', $record) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="fluent-btn fluent-btn-primary fluent-btn-sm" onclick="return confirm('Tandai barang sudah dikembalikan?')">Kembalikan</button>
                                    </form>
                                @else
                                    <span class="text-sm text-neutral-400">Selesai</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="14" class="py-12 text-center text-neutral-400">
                                <p class="text-sm font-medium">Belum ada data peminjaman.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>{{ $records->links() }}</div>
    </div>
</x-app-layout>