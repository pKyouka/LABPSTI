<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-4">
            <h2 class="text-lg font-semibold text-neutral-800">{{ __('Laporan Penggunaan Lab Harian') }}</h2>
            <a href="{{ route('reports.index') }}" class="fluent-btn fluent-btn-secondary fluent-btn-sm">← Kembali</a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="bg-purple-50 border border-purple-200 rounded-xl px-5 py-3 text-sm font-medium text-purple-700">
            Laporan Penggunaan Lab: {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
        </div>

        @if($records->count() === 0)
            <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm p-8 text-center text-neutral-500 text-sm font-medium">Tidak ada penggunaan lab pada hari ini.</div>
        @else
            <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm overflow-hidden">
                <div class="overflow-x-auto">
                <table class="fluent-table">
                    <thead><tr><th>Lab</th><th>Jumlah Pengguna</th><th>Aksi</th></tr></thead>
                    <tbody>
                        @foreach($records as $categoryId => $data)
                            <tr>
                                <td class="font-medium text-neutral-800">{{ $data['category']->name }}</td>
                                <td><span class="fluent-badge fluent-badge-info">{{ $data['count'] }}</span></td>
                                <td><button type="button" onclick="toggleDetails({{ $categoryId }})" class="text-sm font-medium text-accent hover:underline">Lihat Detail</button></td>
                            </tr>
                            <tr id="details-{{ $categoryId }}" style="display:none;">
                                <td colspan="3" class="bg-neutral-50 px-4 py-3">
                                    <table class="w-full text-sm">
                                        <thead><tr class="border-b border-neutral-200"><th class="text-left py-1 px-2 text-xs font-semibold text-neutral-500 uppercase">Nama</th><th class="text-left py-1 px-2 text-xs font-semibold text-neutral-500 uppercase">NIM/ID</th><th class="text-left py-1 px-2 text-xs font-semibold text-neutral-500 uppercase">Waktu</th><th class="text-left py-1 px-2 text-xs font-semibold text-neutral-500 uppercase">Keperluan</th></tr></thead>
                                        <tbody>
                                            @foreach($data['records'] as $record)
                                                <tr><td class="py-1 px-2 text-neutral-700">{{ $record->user_name }}</td><td class="py-1 px-2 text-neutral-600">{{ $record->user_id_number }}</td><td class="py-1 px-2">{{ $record->start_time->format('H:i') }}</td><td class="py-1 px-2 text-xs text-neutral-600">{{ Str::limit($record->purpose,30) }}</td></tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
            </div>
        @endif
    </div>

    @push('scripts')
    <script>function toggleDetails(id){const e=document.getElementById('details-'+id);e.style.display=e.style.display==='none'?'table-row':'none';}</script>
    @endpush
</x-app-layout>
