<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold text-neutral-800">
            {{ __($dashboardTitle) }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        <!-- Stats Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm p-6 hover:shadow-fluent-md transition-shadow">
                <p class="text-xs font-medium text-neutral-400 uppercase tracking-wider mb-3">Total Buletin</p>
                <p class="text-3xl font-bold text-accent">{{ $stats['bulletin_count'] }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm p-6 hover:shadow-fluent-md transition-shadow">
                <p class="text-xs font-medium text-neutral-400 uppercase tracking-wider mb-3">Penggunaan Lab</p>
                <p class="text-3xl font-bold text-accent-700">{{ $stats['usage_count'] }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm p-6 hover:shadow-fluent-md transition-shadow">
                <p class="text-xs font-medium text-neutral-400 uppercase tracking-wider mb-3">Peminjaman</p>
                <p class="text-3xl font-bold text-emerald-600">{{ $stats['borrow_count'] }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm p-6 hover:shadow-fluent-md transition-shadow">
                <p class="text-xs font-medium text-neutral-400 uppercase tracking-wider mb-3">Total Barang</p>
                <p class="text-3xl font-bold text-neutral-800">{{ $stats['item_count'] }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Chart -->
            <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm p-6">
                <h3 class="text-base font-semibold text-neutral-800 mb-1 flex items-center gap-2">
                    <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    {{ $chartTitle }}
                </h3>
                <p class="text-xs text-neutral-400 mb-5">Visualisasi tren penggunaan lab.</p>
                <div style="height: 280px;">
                    <canvas id="usageChart"></canvas>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm p-6">
                <h3 class="text-base font-semibold text-neutral-800 mb-1 flex items-center gap-2">
                    <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    {{ $isAdmin ? 'Aksi Cepat' : 'Menu Mahasiswa' }}
                </h3>
                <p class="text-xs text-neutral-400 mb-5">Akses cepat ke fitur yang sering digunakan.</p>
                <div class="space-y-3">
                    @if($isAdmin)
                        <a href="{{ route('bulletins.create') }}" class="fluent-btn fluent-btn-primary w-full justify-start">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Tambah Buletin Baru
                        </a>
                        <a href="{{ route('usage_records.create') }}" class="fluent-btn fluent-btn-secondary w-full justify-start">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                            Input Penggunaan Lab
                        </a>
                        <a href="{{ route('inventory_items.create') }}" class="fluent-btn fluent-btn-secondary w-full justify-start">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            Tambah Barang Baru
                        </a>
                        <a href="{{ route('borrow_records.create') }}" class="fluent-btn fluent-btn-secondary w-full justify-start">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                            Input Peminjaman
                        </a>
                        <div class="grid grid-cols-2 gap-2 pt-1">
                            <a href="{{ route('bulletins.index') }}" class="fluent-btn fluent-btn-ghost fluent-btn-sm w-full">Kelola Buletin</a>
                            <a href="{{ route('usage_records.index') }}" class="fluent-btn fluent-btn-ghost fluent-btn-sm w-full">Lihat Record</a>
                            <a href="{{ route('inventory_items.index') }}" class="fluent-btn fluent-btn-ghost fluent-btn-sm w-full">Kelola Barang</a>
                            <a href="{{ route('borrow_records.index') }}" class="fluent-btn fluent-btn-ghost fluent-btn-sm w-full">Peminjaman</a>
                        </div>
                    @else
                        <a href="{{ route('bulletins.create') }}" class="fluent-btn fluent-btn-primary w-full justify-start">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Tulis Buletin Saya
                        </a>
                        <a href="{{ route('bulletins.index') }}" class="fluent-btn fluent-btn-secondary w-full justify-start">Lihat Buletin</a>
                        <a href="{{ route('usage_records.create') }}" class="fluent-btn fluent-btn-secondary w-full justify-start">Input Penggunaan Lab</a>
                        <a href="{{ route('borrow_records.create') }}" class="fluent-btn fluent-btn-secondary w-full justify-start">Ajukan Peminjaman</a>
                        <p class="text-xs text-neutral-400 mt-2">Infografis di samping menampilkan data dari akun Anda.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- VM Borrow Table for non-admin --}}
        @if(!$isAdmin && $vmBorrowRequests->isNotEmpty())
        <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm p-6">
            <h3 class="text-base font-semibold text-neutral-800 mb-1 flex items-center gap-2">
                <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                Peminjaman VM Saya
            </h3>
            <p class="text-xs text-neutral-400 mb-4">Status permintaan peminjaman Virtual Machine Anda.</p>
            <table class="fluent-table">
                <thead>
                    <tr>
                        <th>OS</th>
                        <th>Mulai</th>
                        <th>Durasi</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($vmBorrowRequests as $vm)
                    <tr>
                        <td class="text-sm font-medium">{{ strtoupper($vm->os_type) }} {{ $vm->os_version }}</td>
                        <td>{{ $vm->start_date->format('d M Y') }}</td>
                        <td>{{ $vm->duration_days }} hari</td>
                        <td>
                            @if($vm->status === 'approved')
                                <span class="fluent-badge fluent-badge-success">Disetujui</span>
                            @elseif($vm->status === 'rejected')
                                <span class="fluent-badge fluent-badge-danger">Ditolak</span>
                            @else
                                <span class="fluent-badge fluent-badge-warning">Menunggu</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('vm_borrow.show', $vm) }}" class="text-sm font-medium text-accent hover:text-accent-hover">Detail &rarr;</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <a href="{{ route('vm_borrow.index') }}" class="inline-block mt-4 text-sm font-medium text-accent hover:text-accent-hover">Lihat semua &rarr;</a>
        </div>
        @endif
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('usageChart').getContext('2d');
        const usageData = @json($usagePerLab);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: usageData.map(item => item.label),
                datasets: [{
                    label: 'Jumlah Penggunaan',
                    data: usageData.map(item => item.count),
                    backgroundColor: 'rgba(0,120,212,0.35)',
                    borderColor: 'rgba(0,120,212,0.8)',
                    borderWidth: 1,
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, color: '#808080' },
                        grid: { color: 'rgba(0,0,0,0.04)' }
                    },
                    x: {
                        ticks: { color: '#616161' },
                        grid: { display: false }
                    }
                }
            }
        });
    </script>
    @endpush
</x-app-layout>