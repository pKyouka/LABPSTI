<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-4 flex-wrap">
            <div>
                <h2 class="text-lg font-semibold text-neutral-800">Host VM &amp; Proxmox</h2>
                <p class="text-xs text-neutral-400 mt-0.5">
                    Multi-server · resource by hostname
                    @if($activeServer)
                        · {{ $activeServer->name }}
                    @endif
                </p>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('proxmox.servers.create') }}" class="fluent-btn fluent-btn-ghost fluent-btn-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Server
                </a>
                @if($configured && $activeServer)
                <form action="{{ route('proxmox.sync_hosts') }}" method="POST">
                    @csrf
                    <input type="hidden" name="server_id" value="{{ $activeServer->id }}">
                    <button type="submit" class="fluent-btn fluent-btn-ghost fluent-btn-sm" title="Sinkron node → Host VM (by hostname)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Sync Resource
                    </button>
                </form>
                @endif
                <a href="{{ route('vm_hosts.create') }}" class="fluent-btn fluent-btn-ghost fluent-btn-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Host
                </a>
                @if($configured && $activeServer)
                <a href="{{ route('proxmox.create', ['server' => $activeServer->id]) }}" class="fluent-btn fluent-btn-primary fluent-btn-sm">
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
        @if($error ?? null)
            <div class="px-4 py-3 bg-red-50 border border-red-200 text-red-700 text-sm font-medium rounded-xl">{{ $error }}</div>
        @endif

        {{-- Server list (DB) --}}
        <div>
            <div class="flex items-center justify-between mb-3 flex-wrap gap-2">
                <h3 class="text-sm font-semibold text-neutral-700">
                    Server Proxmox
                    <span class="text-neutral-400 font-normal">({{ $servers->count() }})</span>
                </h3>
            </div>

            @if($servers->isEmpty())
            <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm p-8 text-center">
                <div class="w-14 h-14 mx-auto mb-4 rounded-2xl bg-amber-50 grid place-items-center">
                    <svg class="w-7 h-7 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                </div>
                <h3 class="text-base font-semibold text-neutral-800 mb-1">Belum ada server Proxmox</h3>
                <p class="text-sm text-neutral-500 max-w-lg mx-auto mb-4">
                    Kredensial disimpan di database (password terenkripsi). Bisa banyak server.
                </p>
                <a href="{{ route('proxmox.servers.create') }}" class="fluent-btn fluent-btn-primary fluent-btn-sm inline-flex">
                    Tambah Server Pertama
                </a>
                @if($hostsByName->isNotEmpty())
                <div class="mt-8 text-left max-w-3xl mx-auto">
                    <h4 class="text-sm font-semibold text-neutral-700 mb-3">Host VM (manual)</h4>
                    @include('proxmox._hosts_table', ['hosts' => $hostsByName->values()])
                </div>
                @endif
            </div>
            @else
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3">
                @foreach($servers as $srv)
                @php $isActive = $activeServer && $activeServer->id === $srv->id; @endphp
                <div class="bg-white rounded-2xl border {{ $isActive ? 'border-accent shadow-fluent' : 'border-neutral-200 shadow-fluent-sm' }} p-4">
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <div class="min-w-0">
                            <a href="{{ route('proxmox.index', ['server' => $srv->id]) }}" class="font-semibold text-neutral-800 hover:text-accent truncate block">
                                {{ $srv->name }}
                            </a>
                            <p class="text-[11px] text-neutral-400 font-mono truncate" title="{{ $srv->host }}">{{ $srv->host }}</p>
                        </div>
                        <span class="fluent-badge {{ $srv->statusBadgeClass() }} shrink-0">{{ $srv->statusLabel() }}</span>
                    </div>
                    <div class="flex flex-wrap gap-x-3 gap-y-1 text-[11px] text-neutral-500 mb-3">
                        <span>{{ $srv->user }}</span>
                        @if($srv->node)<span>node: <span class="font-mono">{{ $srv->node }}</span></span>@endif
                        <span>{{ $srv->storage }}</span>
                    </div>
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <a href="{{ route('proxmox.index', ['server' => $srv->id]) }}" class="fluent-btn fluent-btn-ghost fluent-btn-sm {{ $isActive ? 'text-accent' : '' }}">
                            {{ $isActive ? 'Dipilih' : 'Buka' }}
                        </a>
                        <a href="{{ route('proxmox.servers.edit', $srv) }}" class="fluent-btn fluent-btn-ghost fluent-btn-sm">Edit</a>
                        <form action="{{ route('proxmox.servers.test', $srv) }}" method="POST">
                            @csrf
                            <button type="submit" class="fluent-btn fluent-btn-ghost fluent-btn-sm">Test</button>
                        </form>
                        <form action="{{ route('proxmox.servers.destroy', $srv) }}" method="POST" onsubmit="return confirm('Hapus server \'{{ $srv->name }}\' dari database?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="fluent-btn fluent-btn-ghost fluent-btn-sm text-red-600 hover:bg-red-50">Hapus</button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        @if($configured && $activeServer)
            {{-- Connection strip --}}
            <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm p-5 flex flex-wrap items-center gap-4 justify-between">
                <div class="flex items-center gap-3">
                    <span class="w-2.5 h-2.5 rounded-full {{ $connection ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                    <div>
                        <p class="text-sm font-semibold text-neutral-800">
                            {{ $connection ? 'Terhubung' : 'Tidak terhubung' }}
                            <span class="text-neutral-400 font-normal">· {{ $activeServer->name }}</span>
                        </p>
                        <p class="text-[12px] text-neutral-400 font-mono">{{ $activeServer->host }}</p>
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
                        <span class="ml-1 font-medium text-neutral-700">{{ $activeServer->user }}</span>
                    </div>
                    <div>
                        <span class="text-neutral-400">Hostname</span>
                        <span class="ml-1 font-medium text-neutral-700">{{ implode(', ', $connection['nodes'] ?? []) }}</span>
                    </div>
                </div>
                @endif
                <a href="{{ route('proxmox.servers.edit', $activeServer) }}" class="fluent-btn fluent-btn-ghost fluent-btn-sm">Edit Server</a>
            </div>

            {{-- Nodes + Host resource (matched by hostname) --}}
            @if(count($nodes) || $orphanHosts->isNotEmpty())
            <div>
                <div class="flex items-center justify-between mb-3 flex-wrap gap-2">
                    <h3 class="text-sm font-semibold text-neutral-700">Host / Node <span class="text-neutral-400 font-normal">(by hostname)</span></h3>
                    @if($filterHost)
                        <a href="{{ route('proxmox.index', ['server' => $activeServer->id]) }}" class="text-[12px] text-accent font-medium hover:underline">Hapus filter · {{ $filterHost }}</a>
                    @endif
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                    @foreach($nodes as $node)
                    @php $host = $node['host'] ?? null; @endphp
                    <div class="bg-white rounded-2xl border {{ $filterHost && strcasecmp($filterHost, $node['name']) === 0 ? 'border-accent shadow-fluent' : 'border-neutral-200 shadow-fluent-sm' }} p-5">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="w-2 h-2 rounded-full shrink-0 {{ $node['status'] === 'online' ? 'bg-emerald-500' : 'bg-red-400' }}"></span>
                                <a href="{{ route('proxmox.index', ['server' => $activeServer->id, 'host' => $node['name']]) }}" class="font-semibold text-neutral-800 truncate hover:text-accent" title="Filter VM di hostname ini">
                                    {{ $node['name'] }}
                                </a>
                            </div>
                            <div class="flex items-center gap-1.5 shrink-0">
                                @if($host)
                                    <span class="fluent-badge {{ $host->statusBadgeClass() }}">{{ $host->statusLabel() }}</span>
                                @endif
                                <span class="fluent-badge {{ $node['status'] === 'online' ? 'fluent-badge-success' : 'fluent-badge-danger' }}">
                                    {{ $node['status'] }}
                                </span>
                            </div>
                        </div>

                        <p class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider mb-1.5">Live Proxmox</p>
                        <div class="space-y-2.5 mb-4">
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

                        @if($host)
                        <div class="pt-3 border-t border-neutral-100">
                            <p class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider mb-1.5">Alokasi Lab</p>
                            <div class="space-y-2">
                                @php
                                    $cpuPct = $host->cpuUsagePercent();
                                    $ramPct = $host->ramUsagePercent();
                                    $storagePct = $host->storageUsagePercent();
                                @endphp
                                <div class="flex items-center gap-2">
                                    <span class="text-[11px] text-neutral-500 w-12 shrink-0">CPU</span>
                                    <div class="flex-1 h-1.5 rounded-full bg-neutral-100 overflow-hidden">
                                        <div class="h-full rounded-full {{ $cpuPct >= 90 ? 'bg-red-500' : 'bg-neutral-400' }}" style="width: {{ min($cpuPct, 100) }}%"></div>
                                    </div>
                                    <span class="text-[11px] text-neutral-500 w-16 text-right">{{ $host->cpu_used }}/{{ $host->cpu_total }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-[11px] text-neutral-500 w-12 shrink-0">RAM</span>
                                    <div class="flex-1 h-1.5 rounded-full bg-neutral-100 overflow-hidden">
                                        <div class="h-full rounded-full {{ $ramPct >= 90 ? 'bg-red-500' : 'bg-neutral-400' }}" style="width: {{ min($ramPct, 100) }}%"></div>
                                    </div>
                                    <span class="text-[11px] text-neutral-500 w-16 text-right">{{ number_format($host->ram_used / 1024, 1) }}/{{ number_format($host->ram_total / 1024, 1) }}G</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-[11px] text-neutral-500 w-12 shrink-0">Disk</span>
                                    <div class="flex-1 h-1.5 rounded-full bg-neutral-100 overflow-hidden">
                                        <div class="h-full rounded-full {{ $storagePct >= 90 ? 'bg-red-500' : 'bg-neutral-400' }}" style="width: {{ min($storagePct, 100) }}%"></div>
                                    </div>
                                    <span class="text-[11px] text-neutral-500 w-16 text-right">{{ $host->storage_used }}/{{ $host->storage_total }}G</span>
                                </div>
                            </div>
                            @if($host->ip_address)
                                <p class="text-[11px] text-neutral-400 font-mono mt-2">{{ $host->ip_address }}</p>
                            @endif
                            <div class="flex items-center justify-end gap-1.5 mt-3 pt-3 border-t border-neutral-100">
                                <a href="{{ route('vm_hosts.edit', $host) }}" class="fluent-btn fluent-btn-ghost fluent-btn-sm">Edit</a>
                                <form action="{{ route('vm_hosts.destroy', $host) }}" method="POST" onsubmit="return confirm('Hapus host \'{{ $host->name }}\' dari alokasi lab? Node Proxmox tidak terpengaruh.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="fluent-btn fluent-btn-ghost fluent-btn-sm text-red-600 hover:bg-red-50">Hapus</button>
                                </form>
                            </div>
                        </div>
                        @else
                        <div class="pt-3 border-t border-neutral-100">
                            <p class="text-[11px] text-amber-600">Belum di-sync ke Host VM. Klik <strong>Sync Resource</strong>.</p>
                        </div>
                        @endif
                    </div>
                    @endforeach

                    @foreach($orphanHosts as $host)
                    <div class="bg-white rounded-2xl border border-dashed border-neutral-300 shadow-fluent-sm p-5 opacity-90">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="w-2 h-2 rounded-full shrink-0 bg-neutral-300"></span>
                                <span class="font-semibold text-neutral-800 truncate">{{ $host->name }}</span>
                            </div>
                            <span class="fluent-badge {{ $host->statusBadgeClass() }}">{{ $host->statusLabel() }}</span>
                        </div>
                        <p class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider mb-1.5">Alokasi Lab · manual</p>
                        <div class="space-y-2">
                            @php
                                $cpuPct = $host->cpuUsagePercent();
                                $ramPct = $host->ramUsagePercent();
                                $storagePct = $host->storageUsagePercent();
                            @endphp
                            <div class="flex items-center gap-2">
                                <span class="text-[11px] text-neutral-500 w-12 shrink-0">CPU</span>
                                <div class="flex-1 h-1.5 rounded-full bg-neutral-100 overflow-hidden">
                                    <div class="h-full rounded-full {{ $cpuPct >= 90 ? 'bg-red-500' : 'bg-neutral-400' }}" style="width: {{ min($cpuPct, 100) }}%"></div>
                                </div>
                                <span class="text-[11px] text-neutral-500 w-16 text-right">{{ $host->cpu_used }}/{{ $host->cpu_total }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-[11px] text-neutral-500 w-12 shrink-0">RAM</span>
                                <div class="flex-1 h-1.5 rounded-full bg-neutral-100 overflow-hidden">
                                    <div class="h-full rounded-full {{ $ramPct >= 90 ? 'bg-red-500' : 'bg-neutral-400' }}" style="width: {{ min($ramPct, 100) }}%"></div>
                                </div>
                                <span class="text-[11px] text-neutral-500 w-16 text-right">{{ number_format($host->ram_used / 1024, 1) }}/{{ number_format($host->ram_total / 1024, 1) }}G</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-[11px] text-neutral-500 w-12 shrink-0">Disk</span>
                                <div class="flex-1 h-1.5 rounded-full bg-neutral-100 overflow-hidden">
                                    <div class="h-full rounded-full {{ $storagePct >= 90 ? 'bg-red-500' : 'bg-neutral-400' }}" style="width: {{ min($storagePct, 100) }}%"></div>
                                </div>
                                <span class="text-[11px] text-neutral-500 w-16 text-right">{{ $host->storage_used }}/{{ $host->storage_total }}G</span>
                            </div>
                        </div>
                        <p class="text-[11px] text-neutral-400 mt-3">Hostname tidak match node server aktif</p>
                        <div class="flex items-center justify-end gap-1.5 mt-2 pt-3 border-t border-neutral-100">
                            <a href="{{ route('vm_hosts.edit', $host) }}" class="fluent-btn fluent-btn-ghost fluent-btn-sm">Edit</a>
                            <form action="{{ route('vm_hosts.destroy', $host) }}" method="POST" onsubmit="return confirm('Hapus host \'{{ $host->name }}\'?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="fluent-btn fluent-btn-ghost fluent-btn-sm text-red-600 hover:bg-red-50">Hapus</button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- VMs --}}
            <div>
                <div class="flex items-center justify-between mb-3 flex-wrap gap-2">
                    <h3 class="text-sm font-semibold text-neutral-700">
                        Virtual Machines &amp; Containers
                        <span class="text-neutral-400 font-normal">({{ count($vms) }})</span>
                        @if($filterHost)
                            <span class="text-accent font-normal">· host {{ $filterHost }}</span>
                        @endif
                    </h3>
                </div>
                <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm overflow-hidden">
                    <div class="overflow-x-auto">
                    <table class="fluent-table">
                        <thead>
                            <tr>
                                <th>VMID</th>
                                <th>Nama</th>
                                <th>Hostname</th>
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
                                    <td>
                                        <a href="{{ route('proxmox.index', ['server' => $activeServer->id, 'host' => $vm['node']]) }}" class="text-neutral-500 text-[13px] hover:text-accent font-mono">{{ $vm['node'] }}</a>
                                    </td>
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
                                                <input type="hidden" name="server_id" value="{{ $activeServer->id }}">
                                                <input type="hidden" name="node" value="{{ $vm['node'] }}">
                                                <input type="hidden" name="vmid" value="{{ $vm['vmid'] }}">
                                                <input type="hidden" name="type" value="{{ $type }}">
                                                <button type="submit" class="fluent-btn fluent-btn-ghost fluent-btn-sm text-emerald-600 hover:bg-emerald-50" title="Start">Start</button>
                                            </form>
                                            @else
                                            <form action="{{ route('proxmox.shutdown') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="server_id" value="{{ $activeServer->id }}">
                                                <input type="hidden" name="node" value="{{ $vm['node'] }}">
                                                <input type="hidden" name="vmid" value="{{ $vm['vmid'] }}">
                                                <input type="hidden" name="type" value="{{ $type }}">
                                                <button type="submit" class="fluent-btn fluent-btn-ghost fluent-btn-sm" title="Shutdown">Shutdown</button>
                                            </form>
                                            <form action="{{ route('proxmox.reboot') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="server_id" value="{{ $activeServer->id }}">
                                                <input type="hidden" name="node" value="{{ $vm['node'] }}">
                                                <input type="hidden" name="vmid" value="{{ $vm['vmid'] }}">
                                                <input type="hidden" name="type" value="{{ $type }}">
                                                <button type="submit" class="fluent-btn fluent-btn-ghost fluent-btn-sm" title="Reboot">Reboot</button>
                                            </form>
                                            <form action="{{ route('proxmox.stop') }}" method="POST" onsubmit="return confirm('Stop paksa VM {{ $vm['vmid'] }}?')">
                                                @csrf
                                                <input type="hidden" name="server_id" value="{{ $activeServer->id }}">
                                                <input type="hidden" name="node" value="{{ $vm['node'] }}">
                                                <input type="hidden" name="vmid" value="{{ $vm['vmid'] }}">
                                                <input type="hidden" name="type" value="{{ $type }}">
                                                <button type="submit" class="fluent-btn fluent-btn-ghost fluent-btn-sm text-amber-600 hover:bg-amber-50" title="Force stop">Stop</button>
                                            </form>
                                            @endif
                                            <form action="{{ route('proxmox.destroy') }}" method="POST" onsubmit="return confirm('HAPUS permanen VM/CT {{ $vm['vmid'] }} ({{ $vm['name'] }})? Tidak bisa dibatalkan.')">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="server_id" value="{{ $activeServer->id }}">
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
                                        <p class="text-sm font-medium">
                                            @if($filterHost)
                                                Tidak ada VM di hostname <strong>{{ $filterHost }}</strong>.
                                            @else
                                                Belum ada VM/CT di cluster.
                                            @endif
                                        </p>
                                        @if(!$filterHost)
                                        <a href="{{ route('proxmox.create', ['server' => $activeServer->id]) }}" class="text-accent font-medium hover:underline mt-1 inline-block">Buat VM sekarang →</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        @elseif($servers->isNotEmpty())
            <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm p-6 text-center text-sm text-neutral-500">
                Pilih server di atas, atau aktifkan salah satu server.
            </div>
        @endif
    </div>
</x-app-layout>
