<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display font-bold text-2xl text-slate-900 leading-tight">
            {{ __($dashboardTitle) }}
        </h2>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="panel-shell">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
                <div class="panel-card p-8 hover:-translate-y-0.5 transition-transform duration-300">
                    <div class="text-[11px] font-black text-slate-400 uppercase tracking-[0.18em] mb-4">Total Buletin</div>
                    <div class="font-display text-4xl font-bold text-cyan-600">{{ $stats['bulletin_count'] }}</div>
                </div>
                <div class="panel-card p-8 hover:-translate-y-0.5 transition-transform duration-300">
                    <div class="text-[11px] font-black text-slate-400 uppercase tracking-[0.18em] mb-4">Total Penggunaan Lab</div>
                    <div class="font-display text-4xl font-bold text-blue-700">{{ $stats['usage_count'] }}</div>
                </div>
                <div class="panel-card p-8 hover:-translate-y-0.5 transition-transform duration-300">
                    <div class="text-[11px] font-black text-slate-400 uppercase tracking-[0.18em] mb-4">Total Peminjaman</div>
                    <div class="font-display text-4xl font-bold text-emerald-600">{{ $stats['borrow_count'] }}</div>
                </div>
                <div class="panel-card p-8 hover:-translate-y-0.5 transition-transform duration-300">
                    <div class="text-[11px] font-black text-slate-400 uppercase tracking-[0.18em] mb-4">Total Barang</div>
                    <div class="font-display text-4xl font-bold text-slate-900">{{ $stats['item_count'] }}</div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Chart Section -->
                <div class="panel-card p-8">
                    <h3 class="text-lg font-black text-slate-900 mb-2 flex items-center">
                        <svg class="w-5 h-5 mr-3 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        {{ $chartTitle }}
                    </h3>
                    <p class="text-sm text-slate-500 mb-6">Visualisasi ringkas untuk memantau tren penggunaan lab.</p>
                    <div style="height: 300px;">
                        <canvas id="usageChart"></canvas>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="panel-card p-8">
                    <h3 class="text-lg font-black text-slate-900 mb-2 flex items-center">
                        <svg class="w-5 h-5 mr-3 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        {{ $isAdmin ? 'Aksi Cepat Admin' : 'Menu Mahasiswa' }}
                    </h3>
                    <p class="text-sm text-slate-500 mb-6">Akses cepat ke fitur yang paling sering digunakan.</p>
                    <div class="grid grid-cols-1 gap-4">
                        @if($isAdmin)
                            <a href="{{ route('bulletins.create') }}" class="inline-flex items-center justify-center px-6 py-4 bg-cyan-600 text-white font-black rounded-2xl hover:bg-cyan-700 transition shadow-lg shadow-cyan-200 uppercase tracking-widest text-xs">
                                Tambah Buletin Baru
                            </a>
                            <a href="{{ route('usage_records.create') }}" class="inline-flex items-center justify-center px-6 py-4 bg-slate-900 text-white font-black rounded-2xl hover:bg-slate-800 transition shadow-lg shadow-slate-300/50 uppercase tracking-widest text-xs">
                                Input Penggunaan Lab
                            </a>
                            <a href="{{ route('inventory_items.create') }}" class="inline-flex items-center justify-center px-6 py-4 bg-emerald-600 text-white font-black rounded-2xl hover:bg-emerald-700 transition shadow-lg shadow-emerald-200 uppercase tracking-widest text-xs">
                                Tambah Barang Baru
                            </a>
                            <a href="{{ route('borrow_records.create') }}" class="inline-flex items-center justify-center px-6 py-4 bg-blue-700 text-white font-black rounded-2xl hover:bg-blue-800 transition shadow-lg shadow-blue-200 uppercase tracking-widest text-xs">
                                Input Peminjaman
                            </a>
                            <div class="grid grid-cols-2 gap-4 mt-2">
                                <a href="{{ route('bulletins.index') }}" class="inline-flex items-center justify-center px-4 py-3 bg-cyan-50 text-cyan-700 font-black rounded-xl hover:bg-cyan-100 transition text-xs">
                                    Kelola Buletin
                                </a>
                                <a href="{{ route('usage_records.index') }}" class="inline-flex items-center justify-center px-4 py-3 bg-cyan-50 text-cyan-700 font-black rounded-xl hover:bg-cyan-100 transition text-xs">
                                    Lihat Record
                                </a>
                                <a href="{{ route('inventory_items.index') }}" class="inline-flex items-center justify-center px-4 py-3 bg-cyan-50 text-cyan-700 font-black rounded-xl hover:bg-cyan-100 transition text-xs">
                                    Kelola Barang
                                </a>
                                <a href="{{ route('borrow_records.index') }}" class="inline-flex items-center justify-center px-4 py-3 bg-cyan-50 text-cyan-700 font-black rounded-xl hover:bg-cyan-100 transition text-xs">
                                    Lihat Peminjaman
                                </a>
                            </div>
                        @else
                            <a href="{{ route('bulletins.create') }}" class="inline-flex items-center justify-center px-6 py-4 bg-slate-900 text-white font-black rounded-2xl hover:bg-slate-800 transition shadow-lg shadow-slate-300/50 uppercase tracking-widest text-xs">
                                Tulis Buletin Saya
                            </a>
                            <a href="{{ route('bulletins.index') }}" class="inline-flex items-center justify-center px-6 py-4 bg-cyan-600 text-white font-black rounded-2xl hover:bg-cyan-700 transition shadow-lg shadow-cyan-200 uppercase tracking-widest text-xs">
                                Lihat Buletin
                            </a>
                            <a href="{{ route('usage_records.create') }}" class="inline-flex items-center justify-center px-6 py-4 bg-emerald-600 text-white font-black rounded-2xl hover:bg-emerald-700 transition shadow-lg shadow-emerald-200 uppercase tracking-widest text-xs">
                                Input Penggunaan Lab
                            </a>
                            <a href="{{ route('borrow_records.create') }}" class="inline-flex items-center justify-center px-6 py-4 bg-blue-700 text-white font-black rounded-2xl hover:bg-blue-800 transition shadow-lg shadow-blue-200 uppercase tracking-widest text-xs">
                                Ajukan Peminjaman
                            </a>
                            <p class="text-sm text-slate-500">Infografis di samping hanya menampilkan data penggunaan dari akun Anda.</p>
                        @endif
                    </div>
                </div>
            </div>

            @if(!$isAdmin && $vmBorrowRequests->isNotEmpty())
            <div class="mt-8">
                <div class="panel-card p-8">
                    <h3 class="text-lg font-black text-slate-900 mb-2 flex items-center">
                        <svg class="w-5 h-5 mr-3 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-4M9 3l6 6M9 3v6h6"></path></svg>
                        Peminjaman VM Saya
                    </h3>
                    <p class="text-sm text-slate-500 mb-6">Status terkini permintaan peminjaman Virtual Machine Anda.</p>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-slate-200">
                                    <th class="text-left py-2 px-3 text-xs font-black text-slate-400 uppercase tracking-wider">OS</th>
                                    <th class="text-left py-2 px-3 text-xs font-black text-slate-400 uppercase tracking-wider">Mulai</th>
                                    <th class="text-left py-2 px-3 text-xs font-black text-slate-400 uppercase tracking-wider">Durasi</th>
                                    <th class="text-left py-2 px-3 text-xs font-black text-slate-400 uppercase tracking-wider">Status</th>
                                    <th class="text-left py-2 px-3 text-xs font-black text-slate-400 uppercase tracking-wider"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($vmBorrowRequests as $vm)
                                <tr class="hover:bg-slate-50">
                                    <td class="py-3 px-3 font-medium text-slate-800">{{ strtoupper($vm->os_type) }} {{ $vm->os_version }}</td>
                                    <td class="py-3 px-3 text-slate-600">{{ $vm->start_date->format('d M Y') }}</td>
                                    <td class="py-3 px-3 text-slate-600">{{ $vm->duration_days }} hari</td>
                                    <td class="py-3 px-3">
                                        @if($vm->status === 'approved')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">Disetujui</span>
                                        @elseif($vm->status === 'rejected')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700">Ditolak</span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-700">Menunggu</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3">
                                        <a href="{{ route('vm_borrow.show', $vm) }}" class="text-cyan-600 hover:text-cyan-800 font-bold text-xs">Detail →</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('vm_borrow.index') }}" class="text-sm text-cyan-600 hover:text-cyan-800 font-bold">Lihat semua →</a>
                    </div>
                </div>
            </div>
            @endif
        </div>
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
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.5)',
                        'rgba(99, 102, 241, 0.5)',
                    ],
                    borderColor: [
                        'rgb(59, 130, 246)',
                        'rgb(99, 102, 241)',
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    </script>
    @endpush
</x-app-layout>
