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
            <a href="{{ route('reports.vm.all', ['status' => '']) }}" class="block bg-violet-500 rounded-xl p-4 text-center shadow-sm hover:bg-violet-600 transition-colors">
                <p class="text-xs font-bold text-violet-100 uppercase tracking-wider">Total VM</p>
                <p class="text-3xl font-bold text-white">{{ $summary['total'] }}</p>
            </a>
            <a href="{{ route('reports.vm.all', ['expiry' => 'active']) }}" class="block bg-emerald-500 rounded-xl p-4 text-center shadow-sm hover:bg-emerald-600 transition-colors">
                <p class="text-xs font-bold text-emerald-100 uppercase tracking-wider">Aktif</p>
                <p class="text-3xl font-bold text-white">{{ $summary['active'] }}</p>
            </a>
            <a href="{{ route('reports.vm.all', ['expiry' => 'expiring_soon']) }}" class="block bg-amber-500 rounded-xl p-4 text-center shadow-sm hover:bg-amber-600 transition-colors">
                <p class="text-xs font-bold text-amber-100 uppercase tracking-wider">Akan Habis</p>
                <p class="text-3xl font-bold text-white">{{ $summary['expiringSoon'] }}</p>
            </a>
            <a href="{{ route('reports.vm.all', ['expiry' => 'expired']) }}" class="block bg-red-500 rounded-xl p-4 text-center shadow-sm hover:bg-red-600 transition-colors">
                <p class="text-xs font-bold text-red-100 uppercase tracking-wider">Expired</p>
                <p class="text-3xl font-bold text-white">{{ $summary['expired'] }}</p>
            </a>
            <a href="{{ route('reports.vm.all', ['status' => 'pending']) }}" class="block bg-gray-500 rounded-xl p-4 text-center shadow-sm hover:bg-gray-600 transition-colors">
                <p class="text-xs font-bold text-gray-100 uppercase tracking-wider">Pending</p>
                <p class="text-3xl font-bold text-white">{{ $summary['pending'] }}</p>
            </a>
        </div>

        <!-- Quick Filter Tabs -->
        <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm p-4">
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-sm font-semibold text-neutral-700 mr-2">Filter Cepat:</span>

                <a href="{{ route('reports.vm.all', array_merge(request()->except(['status', 'expiry']), ['status' => '', 'expiry' => ''])) }}"
                   class="px-4 py-1.5 text-sm font-medium rounded-full transition-colors {{ !request('status') && !request('expiry') ? 'bg-violet-100 text-violet-700 border border-violet-300' : 'bg-neutral-100 text-neutral-600 border border-neutral-200 hover:bg-neutral-200' }}">
                    Semua
                </a>

                <a href="{{ route('reports.vm.all', array_merge(request()->except(['status', 'expiry']), ['status' => 'approved', 'expiry' => ''])) }}"
                   class="px-4 py-1.5 text-sm font-medium rounded-full transition-colors {{ request('status') == 'approved' && !request('expiry') ? 'bg-emerald-100 text-emerald-700 border border-emerald-300' : 'bg-neutral-100 text-neutral-600 border border-neutral-200 hover:bg-neutral-200' }}">
                    ✅ Disetujui
                </a>

                <a href="{{ route('reports.vm.all', array_merge(request()->except(['status', 'expiry']), ['expiry' => 'active'])) }}"
                   class="px-4 py-1.5 text-sm font-medium rounded-full transition-colors {{ request('expiry') == 'active' ? 'bg-emerald-100 text-emerald-700 border border-emerald-300' : 'bg-neutral-100 text-neutral-600 border border-neutral-200 hover:bg-neutral-200' }}">
                    🟢 Masih Aktif
                </a>

                <a href="{{ route('reports.vm.all', array_merge(request()->except(['status', 'expiry']), ['expiry' => 'expiring_soon'])) }}"
                   class="px-4 py-1.5 text-sm font-medium rounded-full transition-colors {{ request('expiry') == 'expiring_soon' ? 'bg-amber-100 text-amber-700 border border-amber-300' : 'bg-neutral-100 text-neutral-600 border border-neutral-200 hover:bg-neutral-200' }}">
                    🟡 Akan Habis
                </a>

                <a href="{{ route('reports.vm.all', array_merge(request()->except(['status', 'expiry']), ['expiry' => 'expired'])) }}"
                   class="px-4 py-1.5 text-sm font-medium rounded-full transition-colors {{ request('expiry') == 'expired' ? 'bg-red-100 text-red-700 border border-red-300' : 'bg-neutral-100 text-neutral-600 border border-neutral-200 hover:bg-neutral-200' }}">
                    🔴 Sudah Expired
                </a>

                <a href="{{ route('reports.vm.all', array_merge(request()->except(['status', 'expiry']), ['status' => 'pending'])) }}"
                   class="px-4 py-1.5 text-sm font-medium rounded-full transition-colors {{ request('status') == 'pending' ? 'bg-blue-100 text-blue-700 border border-blue-300' : 'bg-neutral-100 text-neutral-600 border border-neutral-200 hover:bg-neutral-200' }}">
                    ⏳ Menunggu
                </a>

                <a href="{{ route('reports.vm.all', array_merge(request()->except(['status', 'expiry']), ['status' => 'on-review'])) }}"
                   class="px-4 py-1.5 text-sm font-medium rounded-full transition-colors {{ request('status') == 'on-review' ? 'bg-yellow-100 text-yellow-700 border border-yellow-300' : 'bg-neutral-100 text-neutral-600 border border-neutral-200 hover:bg-neutral-200' }}">
                    🔍 On Review
                </a>

                <a href="{{ route('reports.vm.all', array_merge(request()->except(['status', 'expiry']), ['status' => 'rejected'])) }}"
                   class="px-4 py-1.5 text-sm font-medium rounded-full transition-colors {{ request('status') == 'rejected' ? 'bg-red-100 text-red-700 border border-red-300' : 'bg-neutral-100 text-neutral-600 border border-neutral-200 hover:bg-neutral-200' }}">
                    ❌ Ditolak
                </a>
            </div>
        </div>

        <!-- Advanced Filters (Collapsible) -->
        <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm p-5" x-data="{ open: false }">
            <button @click="open = !open" type="button" class="flex items-center gap-2 text-sm font-medium text-neutral-600 hover:text-neutral-800 transition-colors">
                <svg class="w-4 h-4" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                Filter Lanjutan
            </button>
            <div x-show="open" x-collapse class="mt-4">
                <form method="GET" action="{{ route('reports.vm.all') }}" class="flex flex-wrap items-end gap-3">
                    <div>
                        <x-input-label for="os" value="Sistem Operasi" />
                        <select name="os" id="os" class="fluent-select text-sm">
                            <option value="">Semua OS</option>
                            <option value="debian" {{ request('os') == 'debian' ? 'selected' : '' }}>Debian</option>
                            <option value="ubuntu" {{ request('os') == 'ubuntu' ? 'selected' : '' }}>Ubuntu</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label for="search" value="Cari Peminjam/NIM" />
                        <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Ketik nama atau NIM..." class="fluent-input text-sm">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="fluent-btn fluent-btn-primary fluent-btn-sm">Terapkan Filter</button>
                        <a href="{{ route('reports.vm.all') }}" class="fluent-btn fluent-btn-secondary fluent-btn-sm">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Active Filter Info -->
        @if(request('status') || request('expiry') || request('os') || request('search'))
            <div class="bg-blue-50 border border-blue-200 rounded-xl px-5 py-3 text-sm font-medium text-blue-700 flex items-center gap-2 flex-wrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>Filter aktif:</span>
                @if(request('status'))
                    <span class="bg-white px-2 py-0.5 rounded border border-blue-200 text-xs">
                        Status: {{ match(request('status')) { 'pending' => 'Menunggu', 'on-review' => 'On Review', 'approved' => 'Disetujui', 'rejected' => 'Ditolak', default => request('status') } }}
                    </span>
                @endif
                @if(request('expiry'))
                    <span class="bg-white px-2 py-0.5 rounded border border-blue-200 text-xs">
                        Masa: {{ match(request('expiry')) { 'active' => 'Masih Aktif', 'expiring_soon' => 'Akan Habis', 'expired' => 'Sudah Expired', default => request('expiry') } }}
                    </span>
                @endif
                @if(request('os'))
                    <span class="bg-white px-2 py-0.5 rounded border border-blue-200 text-xs">OS: {{ ucfirst(request('os')) }}</span>
                @endif
                @if(request('search'))
                    <span class="bg-white px-2 py-0.5 rounded border border-blue-200 text-xs">Cari: "{{ request('search') }}"</span>
                @endif
            </div>
        @endif

        <!-- VM Records Table -->
        <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm overflow-hidden">
            @if($vms->count() === 0)
                <div class="p-12 text-center">
                    <svg class="w-16 h-16 mx-auto text-neutral-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    <p class="text-neutral-500 text-sm font-medium">Tidak ada data VM yang ditemukan.</p>
                    <p class="text-neutral-400 text-xs mt-1">Coba ubah filter atau reset pencarian.</p>
                </div>
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
                <div class="mt-3 flex gap-2">
                    <a href="{{ route('reports.vm.all', ['os' => 'debian']) }}" class="text-xs text-accent hover:underline">Filter Debian</a>
                    <span class="text-neutral-300">|</span>
                    <a href="{{ route('reports.vm.all', ['os' => 'ubuntu']) }}" class="text-xs text-accent hover:underline">Filter Ubuntu</a>
                </div>
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

    @push('scripts')
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @endpush
</x-app-layout>
