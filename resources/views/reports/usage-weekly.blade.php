<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-4">
            <h2 class="text-lg font-semibold text-neutral-800">{{ __('Laporan Penggunaan Lab Mingguan') }}</h2>
            <a href="{{ route('reports.index') }}" class="fluent-btn fluent-btn-secondary fluent-btn-sm">← Kembali</a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="bg-pink-50 border border-pink-200 rounded-xl px-5 py-3 text-sm font-medium text-pink-700">
            Laporan Mingguan: {{ $weekStart->locale('id')->isoFormat('D MMMM') }} - {{ $weekEnd->locale('id')->isoFormat('D MMMM YYYY') }}
        </div>

        <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm p-6">
            <h3 class="text-base font-semibold text-neutral-800 mb-4">Statistik Harian</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3">
                @foreach($dailyStats as $dateStr => $stat)
                    <div class="bg-pink-50 rounded-xl p-3 text-center">
                        <p class="text-[10px] font-semibold uppercase text-neutral-500">{{ $stat['date']->locale('id')->isoFormat('ddd') }}</p>
                        <p class="text-xs text-neutral-500">{{ $stat['date']->format('d/m') }}</p>
                        <p class="text-2xl font-bold text-pink-600 mt-1">{{ $stat['count'] }}</p>
                        <p class="text-xs text-neutral-500">penggunaan</p>
                    </div>
                @endforeach
            </div>
        </div>

        @if($records->count() === 0)
            <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm p-8 text-center text-neutral-500 text-sm font-medium">Tidak ada penggunaan lab pada minggu ini.</div>
        @else
            <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-neutral-100"><h3 class="text-base font-semibold text-neutral-800">Detail per Hari</h3></div>
                <div class="p-5 space-y-6">
                    @foreach($records as $dateStr => $dayRecords)
                        <div>
                            <h4 class="text-sm font-semibold text-neutral-800 mb-2">{{ \Carbon\Carbon::parse($dateStr)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</h4>
                            <table class="fluent-table text-sm">
                                <thead><tr><th>Lab</th><th>Pengguna</th><th>NIM</th><th>Waktu</th></tr></thead>
                                <tbody>
                                    @foreach($dayRecords as $record)
                                        <tr>
                                            <td class="font-medium">{{ $record->category?->name }}</td>
                                            <td>{{ $record->user_name }}</td>
                                            <td>{{ $record->user_id_number }}</td>
                                            <td>{{ $record->start_time?->format('H:i') ?? '-' }} {{ $record->end_time ? '- '.$record->end_time->format('H:i') : '' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-app-layout>