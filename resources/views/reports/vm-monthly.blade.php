<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-4">
            <h2 class="text-lg font-semibold text-neutral-800">{{ __('Laporan Penggunaan VM Bulanan') }}</h2>
            <a href="{{ route('reports.index') }}" class="fluent-btn fluent-btn-secondary fluent-btn-sm">← Kembali</a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm p-5">
            <form method="GET" action="{{ route('reports.vm.monthly') }}" id="monthFilterForm" class="flex flex-wrap items-end gap-3">
                <div>
                    <x-input-label for="ms2" value="Bulan" />
                    <select name="month" id="monthSelect" class="fluent-select text-sm" onchange="document.getElementById('monthFilterForm').submit()">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}" {{ $selectedMonth == str_pad($m, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>{{ Carbon\Carbon::create()->month($m)->locale('id')->isoFormat('MMMM') }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <x-input-label for="ys2" value="Tahun" />
                    <select name="year" id="yearSelect" class="fluent-select text-sm" onchange="document.getElementById('monthFilterForm').submit()">
                        @for($y = date('Y') - 5; $y <= date('Y') + 1; $y++)
                            <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('reports.vm.monthly.pdf', ['month' => $selectedMonth, 'year' => $selectedYear]) }}" class="fluent-btn fluent-btn-danger fluent-btn-sm">Export PDF</a>
                </div>
            </form>
        </div>

        <div class="bg-violet-50 border border-violet-200 rounded-xl px-5 py-3 text-sm font-medium text-violet-700">Laporan Bulanan: {{ $monthStart->locale('id')->isoFormat('MMMM YYYY') }}</div>

        <!-- Monthly Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div class="bg-violet-500 rounded-xl p-4 text-center shadow-sm">
                <p class="text-xs font-bold text-violet-100 uppercase tracking-wider mb-1">Total Permintaan</p>
                <p class="text-3xl font-bold text-white">{{ collect($dailyStats)->sum('total') }}</p>
            </div>
            <div class="bg-emerald-500 rounded-xl p-4 text-center shadow-sm">
                <p class="text-xs font-bold text-emerald-100 uppercase tracking-wider mb-1">Disetujui</p>
                <p class="text-3xl font-bold text-white">{{ collect($dailyStats)->sum('approved') }}</p>
            </div>
            <div class="bg-amber-500 rounded-xl p-4 text-center shadow-sm">
                <p class="text-xs font-bold text-amber-100 uppercase tracking-wider mb-1">Pending</p>
                <p class="text-3xl font-bold text-white">{{ collect($dailyStats)->sum('pending') }}</p>
            </div>
            <div class="bg-red-500 rounded-xl p-4 text-center shadow-sm">
                <p class="text-xs font-bold text-red-100 uppercase tracking-wider mb-1">Ditolak</p>
                <p class="text-3xl font-bold text-white">{{ collect($dailyStats)->sum('rejected') }}</p>
            </div>
        </div>

        <!-- VM Lifecycle Summary -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 text-center">
                <p class="text-xs font-bold text-emerald-700 uppercase tracking-wider">VM Dibuat Bulan Ini</p>
                <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $vmsCreatedThisMonth }}</p>
            </div>
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-center">
                <p class="text-xs font-bold text-blue-700 uppercase tracking-wider">VM Aktif Akhir Bulan</p>
                <p class="text-2xl font-bold text-blue-600 mt-1">{{ $vmsActiveEndOfMonth }}</p>
            </div>
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-center">
                <p class="text-xs font-bold text-red-700 uppercase tracking-wider">VM Expired Bulan Ini</p>
                <p class="text-2xl font-bold text-red-600 mt-1">{{ $vmsExpiredThisMonth }}</p>
            </div>
        </div>

        <!-- Weekly Summary -->
        <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm p-6">
            <h3 class="text-base font-semibold text-neutral-800 mb-4">Ringkasan per Minggu</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                @foreach($weeklyStats as $weekKey => $week)
                    <div class="bg-violet-500 rounded-xl p-4 text-center shadow-sm">
                        <p class="text-xs font-bold uppercase text-violet-100">Minggu {{ $loop->iteration }}</p>
                        <p class="text-xs font-medium text-violet-200">{{ $week['start']->format('d/m') }} - {{ $week['end']->format('d/m') }}</p>
                        <p class="text-2xl font-bold text-white mt-1">{{ $week['total'] }}</p>
                        <p class="text-xs font-medium text-violet-100">permintaan</p>
                        <div class="flex justify-center gap-2 mt-1 text-xs text-violet-200">
                            <span>{{ $week['approved'] }} ✅</span>
                            <span>{{ $week['pending'] }} ⏳</span>
                            <span>{{ $week['rejected'] }} ❌</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Daily Stats -->
        <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm p-6">
            <h3 class="text-base font-semibold text-neutral-800 mb-4">Statistik Harian</h3>
            <div class="overflow-x-auto">
                <table class="fluent-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Hari</th>
                            <th class="text-center">Total</th>
                            <th class="text-center">Disetujui</th>
                            <th class="text-center">Pending</th>
                            <th class="text-center">Ditolak</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dailyStats as $dateStr => $stat)
                            <tr>
                                <td class="font-medium">{{ $stat['date']->format('d') }}</td>
                                <td>{{ $stat['date']->locale('id')->isoFormat('dddd') }}</td>
                                <td class="text-center"><span class="font-semibold">{{ $stat['total'] }}</span></td>
                                <td class="text-center">
                                    @if($stat['approved'] > 0)
                                        <span class="fluent-badge fluent-badge-success">{{ $stat['approved'] }}</span>
                                    @else
                                        <span class="text-neutral-300">0</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($stat['pending'] > 0)
                                        <span class="fluent-badge fluent-badge-warning">{{ $stat['pending'] }}</span>
                                    @else
                                        <span class="text-neutral-300">0</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($stat['rejected'] > 0)
                                        <span class="fluent-badge fluent-badge-danger">{{ $stat['rejected'] }}</span>
                                    @else
                                        <span class="text-neutral-300">0</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if($records->count() === 0)
            <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm p-8 text-center text-neutral-500 text-sm font-medium">Tidak ada permintaan VM pada bulan ini.</div>
        @else
            <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm p-5 space-y-6">
                <h3 class="text-base font-semibold text-neutral-800">Detail per Hari</h3>
                @foreach($records as $dateStr => $dayRecords)
                    <div>
                        <h4 class="text-sm font-semibold text-neutral-800 mb-2">{{ \Carbon\Carbon::parse($dateStr)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</h4>
                        <div class="overflow-x-auto">
                            <table class="fluent-table text-sm">
                                <thead>
                                    <tr>
                                        <th>Peminjam</th>
                                        <th>OS</th>
                                        <th>Durasi</th>
                                        <th>Mulai</th>
                                        <th>Berakhir</th>
                                        <th>Status</th>
                                        <th>Keperluan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dayRecords as $record)
                                        <tr>
                                            <td class="font-medium">{{ $record->user->name ?? '-' }}</td>
                                            <td>{{ ucfirst($record->os_type) }} {{ $record->os_version }}</td>
                                            <td>{{ $record->duration_days }} hari</td>
                                            <td>{{ $record->start_date ? $record->start_date->format('d/m/Y') : '-' }}</td>
                                            <td>{{ $record->end_date ? $record->end_date->format('d/m/Y') : '-' }}</td>
                                            <td>
                                                @php
                                                    $badge = match($record->status) {
                                                        'approved' => 'fluent-badge-success',
                                                        'rejected' => 'fluent-badge-danger',
                                                        'on-review' => 'fluent-badge-warning',
                                                        default => 'fluent-badge-info',
                                                    };
                                                    $label = match($record->status) {
                                                        'approved' => 'Disetujui',
                                                        'rejected' => 'Ditolak',
                                                        'on-review' => 'On Review',
                                                        'pending' => 'Menunggu',
                                                        default => $record->status,
                                                    };
                                                @endphp
                                                <span class="fluent-badge {{ $badge }}">{{ $label }}</span>
                                            </td>
                                            <td class="text-xs text-neutral-600 max-w-xs truncate">{{ $record->purpose }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
