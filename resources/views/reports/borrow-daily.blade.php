<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-4">
            <h2 class="text-lg font-semibold text-neutral-800">{{ __('Laporan Peminjaman Harian') }}</h2>
            <a href="{{ route('reports.index') }}" class="fluent-btn fluent-btn-secondary fluent-btn-sm">← Kembali</a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="bg-emerald-50 border border-emerald-200 rounded-xl px-5 py-3 text-sm font-medium text-emerald-700">
            Laporan Peminjaman: {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
        </div>

        @if($records->count() === 0)
            <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm p-8 text-center text-neutral-500 text-sm font-medium">Tidak ada peminjaman pada hari ini.</div>
        @else
            <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm overflow-hidden">
                <table class="fluent-table">
                    <thead><tr><th>Barang</th><th>Jumlah Peminjaman</th><th>Total Kuantitas</th><th>Aksi</th></tr></thead>
                    <tbody>
                        @foreach($records as $itemId => $data)
                            <tr>
                                <td class="font-medium text-neutral-800">{{ $data['item']->name }}</td>
                                <td><span class="fluent-badge fluent-badge-info">{{ $data['count'] }}</span></td>
                                <td><span class="fluent-badge fluent-badge-success">{{ $data['quantity_total'] }}</span></td>
                                <td><button type="button" onclick="toggleDetails({{ $itemId }})" class="text-sm font-medium text-accent hover:underline">Lihat Detail</button></td>
                            </tr>
                            <tr id="details-{{ $itemId }}" style="display:none;">
                                <td colspan="4" class="bg-neutral-50 px-4 py-3">
                                    <table class="w-full text-sm">
                                        <thead><tr class="border-b border-neutral-200"><th class="text-left py-1 px-2 text-xs font-semibold text-neutral-500 uppercase">Peminjam</th><th class="text-left py-1 px-2 text-xs font-semibold text-neutral-500 uppercase">NIM</th><th class="text-left py-1 px-2 text-xs font-semibold text-neutral-500 uppercase">Qty</th><th class="text-left py-1 px-2 text-xs font-semibold text-neutral-500 uppercase">Keperluan</th><th class="text-left py-1 px-2 text-xs font-semibold text-neutral-500 uppercase">Status</th></tr></thead>
                                        <tbody>
                                            @foreach($data['records'] as $record)
                                                <tr><td class="py-1 px-2 text-neutral-700">{{ $record->user_name }}</td><td class="py-1 px-2 text-neutral-600">{{ $record->user_id_number }}</td><td class="py-1 px-2 font-medium">{{ $record->quantity }}</td><td class="py-1 px-2 text-xs text-neutral-600">{{ Str::limit($record->purpose,30) }}</td><td class="py-1 px-2"><span class="fluent-badge {{ $record->status==='dipinjam'?'fluent-badge-info':($record->status==='dikembalikan'?'fluent-badge-success':($record->status==='menunggu'?'fluent-badge-warning':'fluent-badge-danger')) }}">{{ ucfirst($record->status) }}</span></td></tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    @push('scripts')
    <script>function toggleDetails(id){const e=document.getElementById('details-'+id);e.style.display=e.style.display==='none'?'table-row':'none';}</script>
    @endpush
</x-app-layout>