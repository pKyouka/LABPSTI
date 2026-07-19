<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-4 flex-wrap">
            <div>
                <h2 class="text-lg font-semibold text-neutral-800">Proxmox VE</h2>
                <p class="text-xs text-neutral-400 mt-0.5">Koneksi langsung API · {{ config('proxmox.user') }}</p>
            </div>
            <div class="flex items-center gap-2">
                @if($configured)
                <form action="{{ route('proxmox.sync_hosts') }}" method="POST">
                    @csrf
                    <button type="submit" class="fluent-btn fluent-btn-ghost fluent-btn-sm" title="Sinkron node → Host VM">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Sync Host
                    </button>
                </form>
                <a href="{{ route('proxmox.create') }}" class="fluent-btn fluent-btn-primary fluent-btn-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Buat VM
                </a>
                @endif
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
        @if($error)
            <div class="px-4 py-3 bg-red-50 border border-red-200 text-red-700 text-sm font-medium rounded-xl">{{ $error }}</div>
        @endif

        @if(!$configured)
            <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm p-8 text-center">
                <div class="w-14 h-14 mx-auto mb-4 rounded-2xl bg-amber-50 grid place-items-center">
                    <svg class="w-7 h-7 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                </div>
                <h3 class="text-base font-semibold text-neutral-800 mb-1">Proxmox belum dikonfigurasi</h3>
                <p class="text-sm text-neutral-500 max-w-lg mx-auto mb-4">
                    Isi kredensial di file <code class="px-1.5 py-0.5 bg-neutral-100 rounded text-[12px]">.env</code> lalu clear config cache.
                </p>
                <pre class="text-left text-[12px] bg-neutral-900 text-neutral-100 rounded-xl p-4 max-w-xl mx-auto overflow-x-auto leading-relaxed">PROXMOX_HOST=https://192.168.1.10:8006
PROXMOX_USER=root@pam
PROXMOX_PASSWORD=password_root
PROXMOX_NODE=pve
PROXMOX_STORAGE=local-zfs
PROXMOX_VERIFY_SSL=false</pre>
            </div>
        @else
            {{-- Connection strip --}}
            <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm p-5 flex flex-wrap items-center gap-4 justify-between">
                <div class="flex items-center gap-3">
                    <span class="w-2.5 h-2.5 rounded-full {{ $connection ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                    <div>
                        <p class="text-sm font-semibold text-neutral-800">
                            {{ $connection ? 'Terhubung' : 'Tidak terhubung' }}
                        </p>
                        <p class="text-[12px] text-neutral-400 font-mono">{{ config('proxmox.host') }}</p>
                    </div>
                </div>
                @if($connection)
                <div class="flex flex-wrap gap-4 text-[12px] text-neutral-500">
                    <div>
                        <span class="text-neutral-400">Versi</span>
                        <span class="ml-1 font-medium text-neutral-700">{{ $connection['version'] ?? '-' }}{{ isset($connection['release']) ? '/'.$connection['release'] : '' }}</span>
                    </div>
                    <div>
                        <span class="text-neutral-400">User</span>
                        <span class="ml-1 font-medium text-neutral-700">{{ config('proxmox.user') }}</span>
                    </div>
                    <div>
                        <span class="text-neutral-400">Nodes</span>
                        <span class="ml-1 font-medium text-neutral-700">{{ implode(', ', $connection['nodes'] ?? []) }}</span>
                    </div>
                </div>
                @endif
            </div>

            {{-- Nodes --}}
            @if(count($nodes))
            <div>
                <h3 class="text-sm font-semibold text-neutral-700 mb-3">Node</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                    @foreach($nodes as $node)
                    <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm p-5">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full {{ $node['status'] === 'online' ? 'bg-emerald-500' : 'bg-red-400' }}"></span>
                                <span class="font-semibold text-neutral-800">{{ $node['name'] }}</span>
                            </div>
                            <span class="fluent-badge {{ $node['status'] === 'online' ? 'fluent-badge-success' : 'fluent-badge-danger' }}">
                                {{ $node['status'] }}
                            </span>
                        </div>
                        <div class="space-y-2.5">
                            <div>
                                <div class="flex justify-between text-[11px] text-neutral-500 mb-1">
                                    <span>CPU</span>
                                    <span>{{ $node['cpu_pct'] }}% · {{ $node['cpus'] }} cores</span>
                                </div>
                                <div class="h-1.5 rounded-full bg-neutral-100 overflow-hidden">
                                    <div class="h-full rounded-full {{ $node['cpu_pct'] >= 90 ? 'bg-red-500' : 'bg-accent' }}" style="width: {{ min($node['cpu_pct'], 100) }}%"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between text-[11px] text-neutral-500 mb-1">
                                    <span>RAM</span>
                                    <span>{{ $node['mem_used_gb'] }}/{{ $node['mem_total_gb'] }} GB</span>
                                </div>
                                <div class="h-1.5 rounded-full bg-neutral-100 overflow-hidden">
                                    <div class="h-full rounded-full {{ $node['mem_pct'] >= 90 ? 'bg-red-500' : 'bg-neutral-400' }}" style="width: {{ min($node['mem_pct'], 100) }}%"></div>
                                </div>
                            </div>
                            @if($node['uptime'] > 0)
                            @php
                                $d = intdiv($node['uptime'], 86400);
                                $h = intdiv($node['uptime'] % 86400, 3600);
                                $m = intdiv($node['uptime'] % 3600, 60);
                            @endphp
                            <p class="text-[11px] text-neutral-400">Uptime {{ $d }}d {{ $h }}h {{ $m }}m</p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- VMs --}}
            <div>
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-neutral-700">
                        Virtual Machines &amp; Containers
                        <span class="text-neutral-400 font-normal">({{ count($vms) }})</span>
                    </h3>
                </div>
                <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm overflow-hidden">
                    <div class="overflow-x-auto">
                    <table class="fluent-table">
                        <thead>
                            <tr>
                                <th>VMID</th>
                                <th>Nama</th>
                                <th>Node</th>
                                <th>Tipe</th>
                                <th>Status</th>
                                <th>CPU</th>
                                <th>RAM</th>
                                <th>Disk</th>
                                <th class="text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vms as $vm)
                                @php
                                    $memPct = $vm['maxmem'] > 0 ? round(($vm['mem'] / $vm['maxmem']) * 100, 1) : 0;
                                    $diskPct = $vm['maxdisk'] > 0 ? round(($vm['disk'] / $vm['maxdisk']) * 100, 1) : 0;
                                    $cpuPct = round(((float) $vm['cpu']) * 100, 1);
                                    $type = $vm['type'] === 'lxc' ? 'lxc' : 'qemu';
                                @endphp
                                <tr class="{{ $vm['template'] ? 'opacity-60' : '' }}">
                                    <td class="font-mono text-[13px] text-neutral-600">{{ $vm['vmid'] }}</td>
                                    <td class="font-medium text-neutral-800">
                                        {{ $vm['name'] }}
                                        @if($vm['template'])
                                            <span class="ml-1 text-[10px] uppercase tracking-wide text-neutral-400">template</span>
                                        @endif
                                    </td>
                                    <td class="text-neutral-500 text-[13px]">{{ $vm['node'] }}</td>
                                    <td>
                                        <span class="fluent-badge {{ $type === 'lxc' ? 'fluent-badge-info' : '' }}">
                                            {{ strtoupper($type) }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $st = $vm['status'];
                                            $badge = match($st) {
                                                'running' => 'fluent-badge-success',
                                                'stopped' => 'fluent-badge-danger',
                                                default => '',
                                            };
                                        @endphp
                                        <span class="fluent-badge {{ $badge }}">{{ $st }}</span>
                                    </td>
                                    <td class="text-[12px] text-neutral-500">
                                        {{ $cpuPct }}%
                                        <span class="text-neutral-400">/ {{ $vm['cpus'] }}c</span>
                                    </td>
                                    <td class="text-[12px] text-neutral-500">
                                        {{ number_format($vm['mem'] / 1024 / 1024 / 1024, 1) }}/{{ number_format($vm['maxmem'] / 1024 / 1024 / 1024, 1) }}G
                                    </td>
                                    <td class="text-[12px] text-neutral-500">
                                        {{ number_format($vm['maxdisk'] / 1024 / 1024 / 1024, 0) }}G
                                    </td>
                                    <td class="text-right">
                                        @if(!$vm['template'])
                                        <div class="flex items-center justify-end gap-1 flex-wrap">
                                            @if($vm['status'] !== 'running')
                                            <form action="{{ route('proxmox.start') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="node" value="{{ $vm['node'] }}">
                                                <input type="hidden" name="vmid" value="{{ $vm['vmid'] }}">
                                                <input type="hidden" name="type" value="{{ $type }}">
                                                <button type="submit" class="fluent-btn fluent-btn-ghost fluent-btn-sm text-emerald-600 hover:bg-emerald-50" title="Start">Start</button>
                                            </form>
                                            @else
                                            <form action="{{ route('proxmox.shutdown') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="node" value="{{ $vm['node'] }}">
                                                <input type="hidden" name="vmid" value="{{ $vm['vmid'] }}">
                                                <input type="hidden" name="type" value="{{ $type }}">
                                                <button type="submit" class="fluent-btn fluent-btn-ghost fluent-btn-sm" title="Shutdown">Shutdown</button>
                                            </form>
                                            <form action="{{ route('proxmox.reboot') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="node" value="{{ $vm['node'] }}">
                                                <input type="hidden" name="vmid" value="{{ $vm['vmid'] }}">
                                                <input type="hidden" name="type" value="{{ $type }}">
                                                <button type="submit" class="fluent-btn fluent-btn-ghost fluent-btn-sm" title="Reboot">Reboot</button>
                                            </form>
                                            <form action="{{ route('proxmox.stop') }}" method="POST" onsubmit="return confirm('Stop paksa VM {{ $vm['vmid'] }}?')">
                                                @csrf
                                                <input type="hidden" name="node" value="{{ $vm['node'] }}">
                                                <input type="hidden" name="vmid" value="{{ $vm['vmid'] }}">
                                                <input type="hidden" name="type" value="{{ $type }}">
                                                <button type="submit" class="fluent-btn fluent-btn-ghost fluent-btn-sm text-amber-600 hover:bg-amber-50" title="Force stop">Stop</button>
                                            </form>
                                            @endif
                                            <form action="{{ route('proxmox.destroy') }}" method="POST" onsubmit="return confirm('HAPUS permanen VM/CT {{ $vm['vmid'] }} ({{ $vm['name'] }})? Tidak bisa dibatalkan.')">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="node" value="{{ $vm['node'] }}">
                                                <input type="hidden" name="vmid" value="{{ $vm['vmid'] }}">
                                                <input type="hidden" name="type" value="{{ $type }}">
                                                <button type="submit" class="fluent-btn fluent-btn-ghost fluent-btn-sm text-red-600 hover:bg-red-50" title="Delete">Hapus</button>
                                            </form>
                                        </div>
                                        @else
                                        <span class="text-[11px] text-neutral-400">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="py-12 text-center text-neutral-400">
                                        <p class="text-sm font-medium">Belum ada VM/CT di cluster.</p>
                                        <a href="{{ route('proxmox.create') }}" class="text-accent font-medium hover:underline mt-1 inline-block">Buat VM sekarang →</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
