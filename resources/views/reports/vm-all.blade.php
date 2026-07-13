<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-4">
            <h2 class="text-lg font-semibold text-neutral-800">{{ __('Laporan Penggunaan VM') }}</h2>
            <a href="{{ route('reports.index') }}" class="fluent-btn fluent-btn-secondary fluent-btn-sm">← Kembali</a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
            <div class="bg-violet-500 rounded-xl p-4 text-center shadow-sm">
                <p class="text-xs font-bold text-violet-100 uppercase tracking-wider">Total VM</p>
                <p class="text-3xl font-bold text-white">{{ $summary['total'] }}</p>
            </div>
            <div class="bg-emerald-500 rounded-xl p-4 text-center shadow-sm">
                <p class="text-xs font-bold text-emerald-100 uppercase tracking-wider">Aktif</p>
                <p class="text-3xl font-bold text-white">{{ $summary['active'] }}</p>
            </div>
            <div class="bg-amber-500 rounded-xl p-4 text-center shadow-sm">
                <p class="text-xs font-bold text-amber-100 uppercase tracking-wider">Akan Habis</p>
                <p class="text-3xl font-bold text-white">{{ $summary['expiringSoon'] }}</p>
            </div>
            <div class="bg-red-500 rounded-xl p-4 text-center shadow-sm">
                <p class="text-xs font-bold text-red-100 uppercase tracking-wider">Expired</p>
                <p class="text-3xl font-bold text-white">{{ $summary['expired'] }}</p>
            </div>
            <div class="bg-gray-500 rounded-xl p-4 text-center shadow-sm">
                <p class="text-xs font-bold text-gray-100 uppercase tracking-wider">Pending</p>
                <p class="text-3xl font-bold text-white">{{ $summary['pending'] }}</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm p-5">
            <form method="GET" action="{{ route('reports.vm.all') }}" class="flex flex-wrap items-end gap-3">
                <div>
                    <x-input-label for="status" value="Status" />
                    <select name="status" id="status" class="fluent-select text-sm" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
                        <option value="on-review" {{ request('status') == 'on-review' ? 'selected' : '' }}>On Review</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                <div>
                    <x-input-label for="os" value="OS" />
                    <select name="os" id="os" class="fluent-select text-sm" onchange="this.form.submit()">
                        <option value="">Semua OS</option>
                        <option value="debian" {{ request('os') == 'debian' ? 'selected' : '' }}>Debian</option>
                        <option value="ubuntu" {{ request('os') == 'ubuntu' ? 'selected' : '' }}>Ubuntu</option>
                    </select>
                </div>
                <div>
                    <x-input-label for="expiry" value="Masa Berlaku" />
                    <select name="expiry" id="expiry" class="fluent-select text-sm" onchange="this.form.submit()">
                        <option value="">Semua</option>
                        <option value="active" {{ request('expiry') == 'active' ? 'selected' : '' }}>Masih Aktif</option>
                        <option value="expiring_soon" {{ request('expiry') == 'expiring_soon' ? 'selected' : '' }}>Akan Habis (≤7 hari)</option>
                        <option value="expired" {{ request('expiry') == 'expired' ? 'selected' : '' }}>Sudah Expired</option>
                    </select>
                </div>
                <div>
                    <x-input-label for="search" value="Cari Peminjam" />
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Nama..." class="fluent-input text-sm" onchange="this.form.submit()">
                </div>
                <div>
                    <a href="{{ route('reports.vm.all') }}" class="fluent-btn fluent-btn-secondary fluent-btn-sm">Reset</a>
                </div>
            </form>
        </div>

        <!-- VM Records Table -->
        <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm overflow-hidden">
            @if($vms->count() === 0)
                <div class="p-8 text-center text-neutral-500 text-sm font-medium">Tidak ada data VM yang ditemukan.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="fluent-table text-sm">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Peminjam</th>
                                <th>VM (OS)</th>
                                <th>Host IP</th>
                                <th>Durasi</th>
                                <th>Mulai</th>
                                <th>Berakhir</th>
                                <th>Sisa Hari</th>
                                <th>Status Request</th>
                                <th>Status VM</th>
                                <th>Keperluan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vms as $index => $vm)
                                @php
                                    $daysLeft = $vm->end_date ? \Carbon\Carbon::today()->diffInDays($vm->end_date, false) : null;
                                    $isActive = $vm->status === 'approved' && ($daysLeft === null || $daysLeft >= 0);
                                    $isExpired = $vm->status === 'approved' && $daysLeft !== null && $daysLeft < 0;
                                @endphp
                                <tr class="{{ $isExpired ? 'bg-red-50' : ($daysLeft !== null && $daysLeft <= 7 && $daysLeft >= 0 ? 'bg-amber-50' : '') }}">
                                    <td class="text-neutral-500">{{ $vms->firstItem() + $index }}</td>
                                    <td class="font-medium text-neutral-800">
                                        <div>{{ $vm->user->name ?? '-' }}</div>
                                        <div class="text-xs text-neutral-500">{{ $vm->user->id_number ?? '-' }}</div>
                                    </td>
                                    <td>
                                        <span class="font-medium">{{ ucfirst($vm->os_type) }}</span>
                                        <span class="text-neutral-500 text-xs">v{{ $vm->os_version }}</span>
                                    </td>
                                    <td>
                                        @if($vm->host_ip)
                                            <code class="text-xs bg-neutral-100 px-2 py-0.5 rounded">{{ $vm->host_ip }}</code>
                                        @else
                                            <span class="text-neutral-400">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $vm->duration_days }} hari</td>
                                    <td>{{ $vm->start_date ? $vm->start_date->format('d/m/Y') : '-' }}</td>
                                    <td>
                                        @if($vm->end_date)
                                            <span class="{{ $isExpired ? 'text-red-600 font-semibold' : ($daysLeft <= 7 ? 'text-amber-600 font-semibold' : '') }}">
                                                {{ $vm->end_date->format('d/m/Y') }}
                                            </span>
                                        @else
                                            <span class="text-neutral-400">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($daysLeft === null)
                                            <span class="text-neutral-400">-</span>
                                        @elseif($daysLeft < 0)
                                            <span class="fluent-badge fluent-badge-danger">Expired</span>
                                        @elseif($daysLeft === 0)
                                            <span class="fluent-badge fluent-badge-warning">Hari ini</span>
                                        @elseif($daysLeft <= 7)
                                            <span class="fluent-badge fluent-badge-warning">{{ $daysLeft }} hari</span>
                                        @else
                                            <span class="text-neutral-700 font-medium">{{ $daysLeft }} hari</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $badge = match($vm->status) {
                                                'approved' => 'fluent-badge-success',
                                                'rejected' => 'fluent-badge-danger',
                                                'on-review' => 'fluent-badge-warning',
                                                default => 'fluent-badge-info',
                                            };
                                            $label = match($vm->status) {
                                                'approved' => 'Disetujui',
                                                'rejected' => 'Ditolak',
                                                'on-review' => 'On Review',
                                                'pending' => 'Menunggu',
                                                default => $vm->status,
                                            };
                                        @endphp
                                        <span class="fluent-badge {{ $badge }}">{{ $label }}</span>
                                    </td>
                                    <td>
                                        @if($isExpired)
                                            <span class="fluent-badge fluent-badge-danger">Expired</span>
                                        @elseif($isActive)
                                            <span class="fluent-badge fluent-badge-success">Aktif</span>
                                        @elseif($vm->status === 'rejected')
                                            <span class="fluent-badge fluent-badge-danger">-</span>
                                        @else
                                            <span class="text-neutral-400">-</span>
                                        @endif
                                    </td>
                                    <td class="text-xs text-neutral-600 max-w-[150px] truncate" title="{{ $vm->purpose }}">
                                        {{ Str::limit($vm->purpose, 30) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-5 py-3 border-t border-neutral-100">
                    {{ $vms->appends(request()->query())->links() }}
                </div>
            @endif
        </div>

        <!-- Quick Stats by OS -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm p-6">
                <h3 class="text-base font-semibold text-neutral-800 mb-4">Statistik Berdasarkan OS</h3>
                <table class="fluent-table text-sm">
                    <thead>
                        <tr>
                            <th>OS</th>
                            <th class="text-center">Total</th>
                            <th class="text-center">Aktif</th>
                            <th class="text-center">Expired</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($osStats as $os => $stats)
                            <tr>
                                <td class="font-medium capitalize">{{ $os }}</td>
                                <td class="text-center">{{ $stats['total'] }}</td>
                                <td class="text-center"><span class="text-emerald-600 font-medium">{{ $stats['active'] }}</span></td>
                                <td class="text-center"><span class="text-red-600 font-medium">{{ $stats['expired'] }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm p-6">
                <h3 class="text-base font-semibold text-neutral-800 mb-4">VM Akan Habis (7 Hari ke Depan)</h3>
                @if($expiringSoonList->count() === 0)
                    <p class="text-center text-neutral-500 text-sm py-4">Tidak ada VM yang akan habis dalam 7 hari ke depan.</p>
                @else
                    <div class="space-y-2">
                        @foreach($expiringSoonList as $vm)
                            <div class="flex items-center justify-between bg-amber-50 border border-amber-200 rounded-lg px-4 py-2">
                                <div>
                                    <span class="font-medium text-sm">{{ $vm->user->name ?? '-' }}</span>
                                    <span class="text-xs text-neutral-500 ml-2">{{ ucfirst($vm->os_type) }} {{ $vm->os_version }}</span>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs text-neutral-500 block">Berakhir</span>
                                    <span class="text-sm font-semibold text-amber-700">{{ $vm->end_date ? $vm->end_date->format('d/m/Y') : '-' }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
