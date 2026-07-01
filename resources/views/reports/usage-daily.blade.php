<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-4">
            <h2 class="font-bold font-bold text-2xl text-slate-900 leading-tight">
                {{ __('Laporan Penggunaan Lab Harian') }}
            </h2>
            <a href="{{ route('reports.index') }}" class="inline-flex items-center px-6 py-2.5 bg-slate-600 text-white font-black rounded-xl hover:bg-slate-700 transition shadow-lg shadow-slate-200 uppercase tracking-widest text-[10px]">
                Kembali ke Laporan
            </a>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="panel-shell">
            <div class="panel-card overflow-hidden mb-6">
                <div class="panel-card-body">
                    <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 mb-4">
                        <p class="text-sm text-purple-700 font-semibold">
                            Laporan Penggunaan Lab: {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="panel-card overflow-hidden">
                <div class="panel-card-body">
                    @if($records->count() === 0)
                        <div class="text-center py-8">
                            <p class="text-slate-500 font-semibold">Tidak ada penggunaan lab pada hari ini</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-200">
                                <thead class="table-head">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-black text-slate-700 uppercase tracking-wider">Lab</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-black text-slate-700 uppercase tracking-wider">Jumlah Penggunaan</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-black text-slate-700 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200">
                                    @foreach($records as $categoryId => $data)
                                        <tr class="hover:bg-slate-50 transition">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-900">
                                                {{ $data['category']->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-900">
                                                <span class="inline-flex px-3 py-1 text-xs font-black text-white bg-purple-600 rounded-full">
                                                    {{ $data['count'] }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <button type="button" onclick="toggleDetails({{ $categoryId }})" class="text-cyan-600 hover:text-cyan-800 font-black text-xs">
                                                    Lihat Detail
                                                </button>
                                            </td>
                                        </tr>
                                        <tr id="details-{{ $categoryId }}" style="display: none;">
                                            <td colspan="3" class="px-6 py-4 bg-slate-50">
                                                <div class="overflow-x-auto">
                                                    <table class="min-w-full text-sm">
                                                        <thead>
                                                            <tr>
                                                                <th class="text-left px-2 py-2 font-black text-slate-700 text-xs uppercase">Nama Pengguna</th>
                                                                <th class="text-left px-2 py-2 font-black text-slate-700 text-xs uppercase">NIM</th>
                                                                <th class="text-left px-2 py-2 font-black text-slate-700 text-xs uppercase">Jam Mulai</th>
                                                                <th class="text-left px-2 py-2 font-black text-slate-700 text-xs uppercase">Jam Selesai</th>
                                                                <th class="text-left px-2 py-2 font-black text-slate-700 text-xs uppercase">Keperluan</th>
                                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($data['records'] as $record)
                                                <tr>
                                                    <td class="px-2 py-2 text-slate-700">{{ $record->user_name }}</td>
                                                    <td class="px-2 py-2 text-slate-700">{{ $record->user_id_number }}</td>
                                                    <td class="px-2 py-2 text-slate-700 text-xs">{{ $record->start_time->format('H:i') }}</td>
                                                    <td class="px-2 py-2 text-slate-700 text-xs">{{ $record->end_time->format('H:i') }}</td>
                                                    <td class="px-2 py-2 text-slate-700 text-xs">{{ Str::limit($record->purpose, 30) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                                    </table>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function toggleDetails(categoryId) {
            const details = document.getElementById('details-' + categoryId);
            if (details.style.display === 'none') {
                details.style.display = 'table-row';
            } else {
                details.style.display = 'none';
            }
        }
    </script>
    @endpush
</x-app-layout>