<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-4">
            <h2 class="text-lg font-semibold text-neutral-800">
                {{ __('Kelola Host Virtual Machine') }}
            </h2>
            <div class="flex items-center gap-2">
                <form action="{{ route('proxmox.sync_hosts') }}" method="POST">
                    @csrf
                    <button type="submit" class="fluent-btn fluent-btn-ghost fluent-btn-sm" title="Ambil resource live dari Proxmox">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Sync Proxmox
                    </button>
                </form>
                <a href="{{ route('vm_hosts.create') }}" class="fluent-btn fluent-btn-primary fluent-btn-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Host
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if(session('success'))
            <div class="px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium rounded-xl">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="px-4 py-3 bg-red-50 border border-red-200 text-red-700 text-sm font-medium rounded-xl">{{ session('error') }}</div>
        @endif

        <!-- Summary Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm p-5">
                <p class="text-[11px] font-bold text-neutral-400 uppercase tracking-wider mb-1">Total Host</p>
                <p class="text-2xl font-bold text-neutral-800">{{ $hosts->total() }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm p-5">
                <p class="text-[11px] font-bold text-neutral-400 uppercase tracking-wider mb-1">Host Aktif</p>
                @php $activeHosts = $hosts->where('is_active', true)->count(); @endphp
                <p class="text-2xl font-bold text-emerald-600">{{ $activeHosts }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm p-5">
                <p class="text-[11px] font-bold text-neutral-400 uppercase tracking-wider mb-1">Host Penuh</p>
                @php $fullHosts = $hosts->filter(fn($h) => $h->isFull())->count(); @endphp
                <p class="text-2xl font-bold text-red-500">{{ $fullHosts }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm p-5">
                <p class="text-[11px] font-bold text-neutral-400 uppercase tracking-wider mb-1">Host Nonaktif</p>
                @php $inactiveHosts = $hosts->where('is_active', false)->count(); @endphp
                <p class="text-2xl font-bold text-neutral-400">{{ $inactiveHosts }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm overflow-hidden">
            <div class="overflow-x-auto">
            <table class="fluent-table">
                <thead>
                    <tr>
                        <th>Nama Host</th>
                        <th>IP Address</th>
                        <th>CPU</th>
                        <th>RAM</th>
                        <th>Storage</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($hosts as $host)
                        @php
                            $cpuPct = $host->cpuUsagePercent();
                            $ramPct = $host->ramUsagePercent();
                            $storagePct = $host->storageUsagePercent();
                        @endphp
                        <tr>
                            <td class="font-medium text-neutral-800">{{ $host->name }}</td>
                            <td class="text-neutral-500 font-mono text-[13px]">{{ $host->ip_address ?? '-' }}</td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div class="w-16 h-1.5 rounded-full bg-neutral-100 overflow-hidden">
                                        <div class="h-full rounded-full {{ $cpuPct >= 90 ? 'bg-red-500' : 'bg-neutral-400' }}" style="width: {{ min($cpuPct, 100) }}%"></div>
                                    </div>
                                    <span class="text-[12px] text-neutral-500">{{ $host->cpu_used }}/{{ $host->cpu_total }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div class="w-16 h-1.5 rounded-full bg-neutral-100 overflow-hidden">
                                        <div class="h-full rounded-full {{ $ramPct >= 90 ? 'bg-red-500' : 'bg-neutral-400' }}" style="width: {{ min($ramPct, 100) }}%"></div>
                                    </div>
                                    <span class="text-[12px] text-neutral-500">{{ number_format($host->ram_used / 1024, 1) }}/{{ number_format($host->ram_total / 1024, 1) }}G</span>
                                </div>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div class="w-16 h-1.5 rounded-full bg-neutral-100 overflow-hidden">
                                        <div class="h-full rounded-full {{ $storagePct >= 90 ? 'bg-red-500' : 'bg-neutral-400' }}" style="width: {{ min($storagePct, 100) }}%"></div>
                                    </div>
                                    <span class="text-[12px] text-neutral-500">{{ $host->storage_used }}/{{ $host->storage_total }}G</span>
                                </div>
                            </td>
                            <td>
                                <span class="fluent-badge {{ $host->statusBadgeClass() }}">
                                    {{ $host->statusLabel() }}
                                </span>
                            </td>
                            <td class="text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('vm_hosts.edit', $host) }}" class="fluent-btn fluent-btn-ghost fluent-btn-sm">Edit</a>
                                    <form action="{{ route('vm_hosts.destroy', $host) }}" method="POST" onsubmit="return confirm('Hapus host \'{{ $host->name }}\'?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="fluent-btn fluent-btn-ghost fluent-btn-sm text-red-600 hover:bg-red-50">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-neutral-400">
                                <p class="text-sm font-medium">Belum ada host VM yang terdaftar.</p>
                                <a href="{{ route('vm_hosts.create') }}" class="text-accent font-medium hover:underline mt-1 inline-block">Tambah host sekarang →</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>

        <div>{{ $hosts->links() }}</div>
    </div>
</x-app-layout>