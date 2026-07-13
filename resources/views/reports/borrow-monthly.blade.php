<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-4">
            <h2 class="text-lg font-semibold text-neutral-800">{{ __('Laporan Peminjaman Bulanan') }}</h2>
            <a href="{{ route('reports.index') }}" class="fluent-btn fluent-btn-secondary fluent-btn-sm">← Kembali</a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm p-5">
            <form method="GET" action="{{ route('reports.borrow.monthly') }}" id="monthFilterForm" class="flex flex-wrap items-end gap-3">
                <div>
                    <x-input-label for="ms" value="Bulan" />
                    <select name="month" id="monthSelect" class="fluent-select text-sm" onchange="document.getElementById('monthFilterForm').submit()">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}" {{ $selectedMonth == str_pad($m, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>{{ Carbon\Carbon::create()->month($m)->locale('id')->isoFormat('MMMM') }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <x-input-label for="ys" value="Tahun" />
                    <select name="year" id="yearSelect" class="fluent-select text-sm" onchange="document.getElementById('monthFilterForm').submit()">
                        @for($y = date('Y') - 5; $y <= date('Y') + 1; $y++)
                            <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('reports.borrow.monthly.pdf', ['month' => $selectedMonth, 'year' => $selectedYear]) }}" class="fluent-btn fluent-btn-danger fluent-btn-sm">Export PDF</a>
                    <a href="{{ route('reports.borrow.export', ['start_date' => $monthStart->format('Y-m-d'), 'end_date' => $monthEnd->format('Y-m-d')]) }}" class="fluent-btn fluent-btn-primary fluent-btn-sm">Export Excel</a>
                </div>
            </form>
        </div>

        <div class="bg-cyan-50 border border-cyan-200 rounded-xl px-5 py-3 text-sm font-medium text-cyan-700">Laporan Bulanan: {{ $monthStart->locale('id')->isoFormat('MMMM YYYY') }}</div>

        <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm p-6">
            <h3 class="text-base font-semibold text-neutral-800 mb-4">Ringkasan per Minggu</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                @foreach($weeklyStats as $weekKey => $week)
                    <div class="bg-cyan-500 rounded-xl p-4 text-center shadow-sm">
                        <p class="text-xs font-bold uppercase text-cyan-100">Minggu {{ $loop->iteration }}</p>
                        <p class="text-xs font-medium text-cyan-200">{{ $week['start']->format('d/m') }} - {{ $week['end']->format('d/m') }}</p>
                        <p class="text-2xl font-bold text-white mt-1">{{ $week['count'] }}</p>
                        <p class="text-xs font-medium text-cyan-100">peminjaman ({{ $week['quantity'] }} unit)</p>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm p-6">
            <h3 class="text-base font-semibold text-neutral-800 mb-4">Statistik Harian</h3>
            <div class="overflow-x-auto">
            <table class="fluent-table">
                <thead><tr><th>Tanggal</th><th>Hari</th><th class="text-center">Peminjaman</th><th class="text-center">Unit</th></tr></thead>
                <tbody>
                    @foreach($dailyStats as $dateStr => $stat)
                        <tr>
                            <td class="font-medium">{{ $stat['date']->format('d') }}</td>
                            <td>{{ $stat['date']->locale('id')->isoFormat('dddd') }}</td>
                            <td class="text-center"><span class="fluent-badge fluent-badge-info">{{ $stat['count'] }}</span></td>
                            <td class="text-center"><span class="fluent-badge fluent-badge-success">{{ $stat['quantity'] }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
        </div>

        @if($records->count() === 0)
            <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm p-8 text-center text-neutral-500 text-sm font-medium">Tidak ada peminjaman pada bulan ini.</div>
        @else
            <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm p-5 space-y-6">
                <h3 class="text-base font-semibold text-neutral-800">Detail per Hari</h3>
                @foreach($records as $dateStr => $dayRecords)
                    <div>
                        <h4 class="text-sm font-semibold text-neutral-800 mb-2">{{ \Carbon\Carbon::parse($dateStr)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</h4>
                        <div class="overflow-x-auto">
                        <table class="fluent-table text-sm">
                            <thead><tr><th>Barang</th><th>Peminjam</th><th>NIM</th><th>Qty</th><th>Status</th></tr></thead>
                            <tbody>
                                @foreach($dayRecords as $record)
                                    <tr>
                                        <td class="font-medium">{{ $record->inventoryItem?->name }}</td>
                                        <td>{{ $record->user_name }}</td>
                                        <td>{{ $record->user_id_number }}</td>
                                        <td>{{ $record->quantity }}</td>
                                        <td><span class="fluent-badge {{ $record->status==='dipinjam'?'fluent-badge-info':($record->status==='dikembalikan'?'fluent-badge-success':($record->status==='menunggu'?'fluent-badge-warning':'fluent-badge-danger')) }}">{{ ucfirst($record->status) }}</span></td>
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
