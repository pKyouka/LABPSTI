<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-4">
            <h2 class="font-display font-bold text-2xl text-slate-900 leading-tight">
                {{ __('Laporan Penggunaan Lab Mingguan') }}
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
                    <div class="bg-pink-50 border border-pink-200 rounded-lg p-4 mb-4">
                        <p class="text-sm text-pink-700 font-semibold">
                            Laporan Mingguan: {{ $weekStart->locale('id')->isoFormat('D MMMM') }} - {{ $weekEnd->locale('id')->isoFormat('D MMMM YYYY') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Daily Stats Chart -->
            <div class="panel-card overflow-hidden mb-6">
                <div class="panel-card-body">
                    <h3 class="text-lg font-black text-slate-900 mb-4">Statistik Harian</h3>
                    <div class="grid grid-cols-1 md:grid-cols-7 gap-3">
                        @foreach($dailyStats as $dateStr => $stat)
                            <div class="bg-gradient-to-br from-pink-50 to-pink-100 rounded-lg p-4 text-center">
                                <div class="text-[11px] font-black text-slate-500 uppercase tracking-widest mb-2">
                                    {{ $stat['date']->locale('id')->isoFormat('dddd') }}
                                </div>
                                <div class="text-[11px] text-slate-600 mb-3">
                                    {{ $stat['date']->format('d/m') }}
                                </div>
                                <div class="font-display text-2xl font-bold text-pink-600 mb-1">
                                    {{ $stat['count'] }}
                                </div>
                                <div class="text-xs text-slate-600 font-semibold">
                                    penggunaan
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Detailed Records -->
            <div class="panel-card overflow-hidden">
                <div class="panel-card-body">
                    @if($records->count() === 0)
                        <div class="text-center py-8">
                            <p class="text-slate-500 font-semibold">Tidak ada penggunaan lab pada minggu ini</p>
                        </div>
                    @else
                        <h3 class="text-lg font-black text-slate-900 mb-4">Detail Penggunaan per Hari</h3>
                        @foreach($records as $dateStr => $dayRecords)
                            <div class="mb-6 pb-6 border-b border-slate-200 last:border-b-0">
                                <h4 class="font-black text-slate-900 mb-3">
                                    {{ \Carbon\Carbon::parse($dateStr)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
                                </h4>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full text-sm divide-y divide-slate-200">
                                        <thead>
                                            <tr>
                                                <th class="text-left px-3 py-2 font-black text-slate-700 text-xs uppercase">Lab</th>
                                                <th class="text-left px-3 py-2 font-black text-slate-700 text-xs uppercase">Pengguna</th>
                                                <th class="text-left px-3 py-2 font-black text-slate-700 text-xs uppercase">NIM</th>
                                                <th class="text-left px-3 py-2 font-black text-slate-700 text-xs uppercase">Waktu</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-200">
                                            @foreach($dayRecords as $record)
                                                <tr class="hover:bg-slate-50">
                                                    <td class="px-3 py-2 font-semibold text-slate-900">{{ $record->category?->name }}</td>
                                                    <td class="px-3 py-2 text-slate-700">{{ $record->user_name }}</td>
                                                    <td class="px-3 py-2 text-slate-700">{{ $record->user_id_number }}</td>
                                                    <td class="px-3 py-2 text-slate-700 text-xs">
                                                        {{ $record->start_time->format('H:i') }} - {{ $record->end_time->format('H:i') }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>