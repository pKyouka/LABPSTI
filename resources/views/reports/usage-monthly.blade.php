<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-4">
            <h2 class="font-display font-bold text-2xl text-slate-900 leading-tight">
                {{ __('Laporan Penggunaan Lab Bulanan') }}
            </h2>
            <a href="{{ route('reports.index') }}" class="inline-flex items-center px-6 py-2.5 bg-slate-600 text-white font-black rounded-xl hover:bg-slate-700 transition shadow-lg shadow-slate-200 uppercase tracking-widest text-[10px]">
                Kembali ke Laporan
            </a>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="panel-shell">
            <!-- Month Selector -->
            <div class="panel-card overflow-hidden mb-6">
                <div class="panel-card-body">
                    <form method="GET" action="{{ route('reports.usage.monthly') }}" class="flex flex-wrap items-end gap-4">
                        <div>
                            <label class="block text-xs font-black text-slate-700 uppercase tracking-widest mb-2">Bulan</label>
                            <select name="month" class="rounded-lg border-slate-300 text-sm focus:border-rose-500 focus:ring-rose-500">
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}" {{ $selectedMonth == str_pad($m, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                        {{ Carbon\Carbon::create()->month($m)->locale('id')->isoFormat('MMMM') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-black text-slate-700 uppercase tracking-widest mb-2">Tahun</label>
                            <select name="year" class="rounded-lg border-slate-300 text-sm focus:border-rose-500 focus:ring-rose-500">
                                @for($y = date('Y') - 5; $y <= date('Y') + 1; $y++)
                                    <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-rose-600 text-white font-black rounded-lg hover:bg-rose-700 transition text-xs uppercase tracking-widest">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                Lihat Laporan
                            </button>
                        </div>
                        <div>
                            <a href="{{ route('reports.usage.monthly.pdf', ['month' => $selectedMonth, 'year' => $selectedYear]) }}" class="inline-flex items-center px-6 py-2.5 bg-red-600 text-white font-black rounded-lg hover:bg-red-700 transition text-xs uppercase tracking-widest">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                Export PDF
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="panel-card overflow-hidden mb-6">
                <div class="panel-card-body">
                    <div class="bg-rose-50 border border-rose-200 rounded-lg p-4 mb-4">
                        <p class="text-sm text-rose-700 font-semibold">
                            Laporan Bulanan: {{ $monthStart->locale('id')->isoFormat('MMMM YYYY') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Weekly Summary -->
            <div class="panel-card overflow-hidden mb-6">
                <div class="panel-card-body">
                    <h3 class="text-lg font-black text-slate-900 mb-4">Ringkasan per Minggu</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        @foreach($weeklyStats as $weekKey => $week)
                            <div class="bg-gradient-to-br from-rose-50 to-rose-100 rounded-lg p-4">
                                <div class="text-[11px] font-black text-slate-600 uppercase tracking-widest mb-2">
                                    Minggu {{ $loop->iteration }}
                                </div>
                                <div class="text-xs text-slate-600 mb-3">
                                    {{ $week['start']->format('d/m') }} - {{ $week['end']->format('d/m/Y') }}
                                </div>
                                <div class="font-display text-2xl font-bold text-rose-600 mb-1">
                                    {{ $week['count'] }}
                                </div>
                                <div class="text-xs text-slate-600 font-semibold">
                                    penggunaan
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Daily Stats -->
            <div class="panel-card overflow-hidden mb-6">
                <div class="panel-card-body">
                    <h3 class="text-lg font-black text-slate-900 mb-4">Statistik Harian</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm divide-y divide-slate-200">
                            <thead>
                                <tr class="bg-slate-50">
                                    <th class="text-left px-4 py-3 font-black text-slate-700 text-xs uppercase">Tanggal</th>
                                    <th class="text-left px-4 py-3 font-black text-slate-700 text-xs uppercase">Hari</th>
                                    <th class="text-center px-4 py-3 font-black text-slate-700 text-xs uppercase">Jumlah Penggunaan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                @foreach($dailyStats as $dateStr => $stat)
                                    <tr class="hover:bg-slate-50">
                                        <td class="px-4 py-3 font-semibold text-slate-900">{{ $stat['date']->format('d') }}</td>
                                        <td class="px-4 py-3 text-slate-700">{{ $stat['date']->locale('id')->isoFormat('dddd') }}</td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="inline-flex px-3 py-1 text-xs font-black text-white bg-rose-600 rounded-full">
                                                {{ $stat['count'] }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Detailed Records -->
            <div class="panel-card overflow-hidden">
                <div class="panel-card-body">
                    @if($records->count() === 0)
                        <div class="text-center py-8">
                            <p class="text-slate-500 font-semibold">Tidak ada penggunaan lab pada bulan ini</p>
                        </div>
                    @else
                        <h3 class="text-lg font-black text-slate-900 mb-4">Detail Penggunaan per Hari</h3>
                        @foreach($records as $dateStr => $dayRecords)
                            <div class="mb-4 pb-4 border-b border-slate-200 last:border-b-0">
                                <h4 class="font-black text-slate-900 mb-2">
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
