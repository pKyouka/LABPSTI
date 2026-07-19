{{-- Fallback table saat Proxmox offline — $hosts = collection VmHost --}}
<div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="fluent-table">
            <thead>
                <tr>
                    <th>Hostname</th>
                    <th>IP</th>
                    <th>CPU</th>
                    <th>RAM</th>
                    <th>Storage</th>
                    <th>Status</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($hosts as $host)
                    @php
                        $cpuPct = $host->cpuUsagePercent();
                        $ramPct = $host->ramUsagePercent();
                        $storagePct = $host->storageUsagePercent();
                    @endphp
                    <tr>
                        <td class="font-medium text-neutral-800 font-mono">{{ $host->name }}</td>
                        <td class="text-neutral-500 font-mono text-[13px]">{{ $host->ip_address ?? '-' }}</td>
                        <td class="text-[12px] text-neutral-500">{{ $host->cpu_used }}/{{ $host->cpu_total }}</td>
                        <td class="text-[12px] text-neutral-500">{{ number_format($host->ram_used / 1024, 1) }}/{{ number_format($host->ram_total / 1024, 1) }}G</td>
                        <td class="text-[12px] text-neutral-500">{{ $host->storage_used }}/{{ $host->storage_total }}G</td>
                        <td><span class="fluent-badge {{ $host->statusBadgeClass() }}">{{ $host->statusLabel() }}</span></td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-1.5 flex-wrap">
                                @if($host->ip_address)
                                <a href="{{ route('proxmox.servers.create', [
                                    'name' => 'Proxmox '.$host->name,
                                    'host' => $host->ip_address,
                                    'node' => $host->name,
                                ]) }}" class="fluent-btn fluent-btn-ghost fluent-btn-sm text-accent" title="Daftarkan IP ini sebagai Server Proxmox">
                                    Daftarkan Server
                                </a>
                                @endif
                                <a href="{{ route('vm_hosts.edit', $host) }}" class="fluent-btn fluent-btn-ghost fluent-btn-sm">Edit</a>
                                <form action="{{ route('vm_hosts.destroy', $host) }}" method="POST" onsubmit="return confirm('Hapus host \'{{ $host->name }}\'?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="fluent-btn fluent-btn-ghost fluent-btn-sm text-red-600 hover:bg-red-50">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
