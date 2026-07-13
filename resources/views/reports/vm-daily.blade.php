<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-4">
            <h2 class="text-lg font-semibold text-neutral-800">{{ __('Laporan Penggunaan VM Harian') }}</h2>
            <a href="{{ route('reports.index') }}" class="fluent-btn fluent-btn-secondary fluent-btn-sm">← Kembali</a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="bg-violet-50 border border-violet-200 rounded-xl px-5 py-3 text-sm font-medium text-violet-700">
            Laporan Penggunaan VM: {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
        </div>

        <!-- Active VMs Summary -->
        <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm p-6">
            <h3 class="text-base font-semibold text-neutral-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                VM Aktif Saat Ini
            </h3>
            @if($activeVms->count() === 0)
                <div class="text-center text-neutral-500 text-sm font-medium py-4">Tidak ada VM yang aktif saat ini.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="fluent-table text-sm">
                        <thead>
                            <tr>
                                <th>Peminjam</th>
                                <th>OS</th>
                                <th>Host IP</th>
                                <th>Mulai</th>
                                <th>Berakhir</th>
                                <th>Sisa Hari</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($activeVms as $vm)
                                @php
                                    $daysLeft = $vm->end_date ? \Carbon\Carbon::today()->diffInDays($vm->end_date, false) : null;
                                @endphp
                                <tr>
                                    <td class="font-medium text-neutral-800">{{ $vm->user->name ?? '-' }}</td>
                                    <td>{{ ucfirst($vm->os_type) }} {{ $vm->os_version }}</td>
                                    <td><code class="text-xs bg-neutral-100 px-2 py-0.5 rounded">{{ $vm->host_ip ?? '-' }}</code></td>
                                    <td>{{ $vm->start_date ? $vm->start_date->format('d/m/Y') : '-' }}</td>
                                    <td>{{ $vm->end_date ? $vm->end_date->format('d/m/Y') : '-' }}</td>
                                    <td>
                                        @if($daysLeft === null)
                                            <span class="text-neutral-400">-</span>
                                        @elseif($daysLeft < 0)
                                            <span class="fluent-badge fluent-badge-danger">Expired</span>
                                        @elseif($daysLeft <= 7)
                                            <span class="fluent-badge fluent-badge-warning">{{ $daysLeft }} hari</span>
                                        @else
                                            <span class="text-neutral-700">{{ $daysLeft }} hari</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($daysLeft !== null && $daysLeft < 0)
                                            <span class="fluent-badge fluent-badge-danger">Expired</span>
                                        @else
                                            <span class="fluent-badge fluent-badge-success">Aktif</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Today's Requests -->
        <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm p-6">
            <h3 class="text-base font-semibold text-neutral-800 mb-4">Permintaan VM Hari Ini</h3>
            @if($records->flatten()->count() === 0)
                <div class="text-center text-neutral-500 text-sm font-medium py-4">Tidak ada permintaan VM pada hari ini.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="fluent-table text-sm">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th class="text-center">Jumlah</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(['pending' => 'Menunggu', 'on-review' => 'On Review', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'] as $statusKey => $statusLabel)
                                @if(isset($records[$statusKey]))
                                    <tr>
                                        <td>
                                            @php
                                                $badgeClass = match($statusKey) {
                                                    'approved' => 'fluent-badge-success',
                                                    'rejected' => 'fluent-badge-danger',
                                                    'on-review' => 'fluent-badge-warning',
                                                    default => 'fluent-badge-info',
                                                };
                                            @endphp
                                            <span class="fluent-badge {{ $badgeClass }}">{{ $statusLabel }}</span>
                                        </td>
                                        <td class="text-center"><span class="font-bold text-lg">{{ $records[$statusKey]['count'] }}</span></td>
                                        <td>
                                            <button type="button" onclick="toggleDetails('{{ $statusKey }}')" class="text-sm font-medium text-accent hover:underline">Lihat Detail</button>
                                        </td>
                                    </tr>
                                    <tr id="details-{{ $statusKey }}" style="display:none;">
                                        <td colspan="3" class="bg-neutral-50 px-4 py-3">
                                            <table class="w-full text-sm">
                                                <thead>
                                                    <tr class="border-b border-neutral-200">
                                                        <th class="text-left py-1 px-2 text-xs font-semibold text-neutral-500 uppercase">Peminjam</th>
                                                        <th class="text-left py-1 px-2 text-xs font-semibold text-neutral-500 uppercase">OS</th>
                                                        <th class="text-left py-1 px-2 text-xs font-semibold text-neutral-500 uppercase">Durasi</th>
                                                        <th class="text-left py-1 px-2 text-xs font-semibold text-neutral-500 uppercase">Keperluan</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($records[$statusKey]['records'] as $record)
                                                        <tr>
                                                            <td class="py-1 px-2 text-neutral-700">{{ $record->user->name ?? '-' }}</td>
                                                            <td class="py-1 px-2 text-neutral-600">{{ ucfirst($record->os_type) }} {{ $record->os_version }}</td>
                                                            <td class="py-1 px-2">{{ $record->duration_days }} hari</td>
                                                            <td class="py-1 px-2 text-xs text-neutral-600">{{ Str::limit($record->purpose, 40) }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>function toggleDetails(id){const e=document.getElementById('details-'+id);e.style.display=e.style.display==='none'?'table-row':'none';}</script>
    @endpush
</x-app-layout>
