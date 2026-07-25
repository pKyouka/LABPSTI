<?php

namespace App\Http\Controllers;

use App\Models\ProxmoxServer;
use App\Models\VmHost;
use App\Services\ProxmoxService;
use Illuminate\Http\Request;
use RuntimeException;

class ProxmoxController extends Controller
{
    public function __construct(protected ProxmoxService $proxmox) {}

    public function index(Request $request)
    {
        // One-shot: kalau DB kosong tapi .env masih punya kredensial → import
        $this->bootstrapFromEnvIfEmpty();

        $servers = ProxmoxServer::orderBy('name')->get();
        $filterHost = $request->query('host');
        $activeServer = $this->resolveServer($request, $servers);
        $configured = (bool) $activeServer;
        $error = null;
        $connection = null;
        $nodes = [];
        $vms = [];

        $hostsByName = VmHost::orderBy('name')->get()->keyBy(fn (VmHost $h) => strtolower($h->name));

        if ($activeServer) {
            $svc = $this->proxmox->forServer($activeServer);

            try {
                $connection = $svc->testConnection();

                $nodes = collect($svc->getNodes())->map(function (array $n) use ($svc, $hostsByName) {
                    $status = [];

                    try {
                        $status = $svc->getNodeStatus($n['node']);
                    } catch (\Throwable) {
                        // node offline
                    }

                    $memTotal = $status['memory']['total'] ?? $n['maxmem'] ?? 0;
                    $memUsed = $status['memory']['used'] ?? $n['mem'] ?? 0;
                    $cpu = $status['cpu'] ?? $n['cpu'] ?? 0;
                    $name = $n['node'];
                    $host = $hostsByName->get(strtolower($name));

                    return [
                        'name' => $name,
                        'status' => $n['status'] ?? 'unknown',
                        'cpu_pct' => round(((float) $cpu) * 100, 1),
                        'cpus' => $status['cpuinfo']['cpus'] ?? $n['maxcpu'] ?? 0,
                        'mem_used_gb' => round($memUsed / 1024 / 1024 / 1024, 1),
                        'mem_total_gb' => round($memTotal / 1024 / 1024 / 1024, 1),
                        'mem_pct' => $memTotal > 0 ? round(($memUsed / $memTotal) * 100, 1) : 0,
                        'uptime' => $status['uptime'] ?? $n['uptime'] ?? 0,
                        'host' => $host,
                    ];
                })->all();

                $vms = $svc->getClusterVms();
            } catch (RuntimeException $e) {
                $error = $e->getMessage();
            } catch (\Throwable $e) {
                $error = 'Gagal terhubung ke Proxmox: '.$e->getMessage();
            }
        }

        $nodeNames = collect($nodes)->pluck('name')->map(fn ($n) => strtolower($n))->all();

        $orphanHosts = $hostsByName
            ->reject(fn (VmHost $h) => in_array(strtolower($h->name), $nodeNames, true))
            ->values();

        if ($filterHost) {
            $vms = collect($vms)
                ->filter(fn ($vm) => strcasecmp((string) ($vm['node'] ?? ''), $filterHost) === 0)
                ->values()
                ->all();
        }

        return view('proxmox.index', compact(
            'servers',
            'activeServer',
            'configured',
            'connection',
            'nodes',
            'vms',
            'error',
            'hostsByName',
            'orphanHosts',
            'filterHost'
        ));
    }

    public function create(Request $request)
    {
        $servers = ProxmoxServer::active()->orderBy('name')->get();
        $activeServer = $this->resolveServer($request, $servers);
        $nodes = [];
        $nextId = null;
        $error = null;
        $templateVmid = null;
        $storage = 'local-zfs';
        $bridge = 'vmbr0';
        $defaultNode = null;

        if (! $activeServer) {
            $error = 'Belum ada server Proxmox aktif. Tambah server dulu.';
        } else {
            $svc = $this->proxmox->forServer($activeServer);
            $templateVmid = $svc->defaultTemplateVmid();
            $storage = $svc->defaultStorage();
            $bridge = $svc->defaultBridge();
            $defaultNode = $svc->defaultNode();

            try {
                $nodes = collect($svc->getNodes())->pluck('node')->all();
                $nextId = $svc->getNextId();
            } catch (\Throwable $e) {
                $error = $e->getMessage();
            }
        }

        return view('proxmox.create', [
            'servers' => $servers,
            'activeServer' => $activeServer,
            'nodes' => $nodes,
            'nextId' => $nextId,
            'error' => $error,
            'templateVmid' => $templateVmid,
            'defaults' => config('proxmox.defaults'),
            'defaultNode' => $defaultNode,
            'storage' => $storage,
            'bridge' => $bridge,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'server_id' => 'required|exists:proxmox_servers,id',
            'mode' => 'required|in:blank,clone',
            'node' => 'required|string|max:64',
            'name' => 'required|string|max:64|regex:/^[A-Za-z0-9][A-Za-z0-9.\-_]*$/',
            'vmid' => 'nullable|integer|min:100|max:999999999',
            'cores' => 'required|integer|min:1|max:128',
            'memory' => 'required|integer|min:512|max:1048576',
            'disk' => 'required|integer|min:1|max:10240',
            'storage' => 'nullable|string|max:64',
            'bridge' => 'nullable|string|max:64',
            'template_vmid' => 'nullable|integer|min:100',
            'description' => 'nullable|string|max:500',
            'start_after' => 'boolean',
        ]);

        $server = ProxmoxServer::findOrFail($data['server_id']);
        $svc = $this->proxmox->forServer($server);

        try {
            if ($data['mode'] === 'clone') {
                $result = $svc->cloneTemplate([
                    'node' => $data['node'],
                    'vmid' => $data['vmid'] ?? null,
                    'name' => $data['name'],
                    'cores' => $data['cores'],
                    'memory' => $data['memory'],
                    'storage' => $data['storage'] ?? $svc->defaultStorage(),
                    'template_vmid' => $data['template_vmid'] ?? $svc->defaultTemplateVmid(),
                    'description' => $data['description'] ?? null,
                ]);
            } else {
                $result = $svc->createVm([
                    'node' => $data['node'],
                    'vmid' => $data['vmid'] ?? null,
                    'name' => $data['name'],
                    'cores' => $data['cores'],
                    'memory' => $data['memory'],
                    'disk' => $data['disk'],
                    'storage' => $data['storage'] ?? $svc->defaultStorage(),
                    'bridge' => $data['bridge'] ?? $svc->defaultBridge(),
                    'description' => $data['description'] ?? null,
                ]);
            }

            if ($request->boolean('start_after') && $data['mode'] === 'clone') {
                try {
                    $svc->startVm($result['node'], $result['vmid']);
                } catch (\Throwable) {
                    // clone mungkin masih running task
                }
            }

            return redirect()
                ->route('proxmox.index', ['server' => $server->id])
                ->with('success', "VM {$result['name']} (ID {$result['vmid']}) berhasil dibuat di node {$result['node']}.");
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function start(Request $request)
    {
        return $this->powerAction($request, 'start');
    }

    public function stop(Request $request)
    {
        return $this->powerAction($request, 'stop');
    }

    public function shutdown(Request $request)
    {
        return $this->powerAction($request, 'shutdown');
    }

    public function reboot(Request $request)
    {
        return $this->powerAction($request, 'reboot');
    }

    public function destroy(Request $request)
    {
        $data = $request->validate([
            'server_id' => 'required|exists:proxmox_servers,id',
            'node' => 'required|string|max:64',
            'vmid' => 'required|integer|min:100',
            'type' => 'required|in:qemu,lxc',
        ]);

        try {
            $svc = $this->proxmox->forServer(ProxmoxServer::findOrFail($data['server_id']));
            $svc->deleteVm($data['node'], $data['vmid'], $data['type']);

            return back()->with('success', "VM/CT {$data['vmid']} dihapus dari node {$data['node']}.");
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function syncHosts(Request $request)
    {
        $data = $request->validate([
            'server_id' => 'required|exists:proxmox_servers,id',
        ]);

        try {
            $server = ProxmoxServer::findOrFail($data['server_id']);

            // Prefix server name bila multi-server agar hostname unik
            $multi = ProxmoxServer::count() > 1;
            $hosts = $this->proxmox->forServer($server)->syncHosts(prefixServer: $multi);
            $count = count($hosts);

            return redirect()
                ->route('proxmox.index', ['server' => $server->id])
                ->with('success', "Berhasil sinkron {$count} node dari \"{$server->name}\" ke Host VM.");
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function test(Request $request)
    {
        $serverId = $request->query('server');

        try {
            $server = $serverId ? ProxmoxServer::findOrFail($serverId) : ProxmoxServer::active()->orderBy('name')->first();

            if (! $server) {
                return response()->json(['ok' => false, 'error' => 'Tidak ada server Proxmox.'], 422);
            }

            $result = $this->proxmox->forServer($server)->testConnection();

            return response()->json(['ok' => true, 'data' => $result]);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 422);
        }
    }

    protected function powerAction(Request $request, string $action)
    {
        $data = $request->validate([
            'server_id' => 'required|exists:proxmox_servers,id',
            'node' => 'required|string|max:64',
            'vmid' => 'required|integer|min:100',
            'type' => 'required|in:qemu,lxc',
        ]);

        try {
            $svc = $this->proxmox->forServer(ProxmoxServer::findOrFail($data['server_id']));

            match ($action) {
                'start' => $svc->startVm($data['node'], $data['vmid'], $data['type']),
                'stop' => $svc->stopVm($data['node'], $data['vmid'], $data['type']),
                'shutdown' => $svc->shutdownVm($data['node'], $data['vmid'], $data['type']),
                'reboot' => $svc->rebootVm($data['node'], $data['vmid'], $data['type']),
            };

            $labels = [
                'start' => 'dihidupkan',
                'stop' => 'di-stop paksa',
                'shutdown' => 'di-shutdown',
                'reboot' => 'di-reboot',
            ];

            return back()->with('success', "VM {$data['vmid']} berhasil {$labels[$action]}.");
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * @param \Illuminate\Support\Collection<int, ProxmoxServer> $servers
     */
    protected function resolveServer(Request $request, $servers): ?ProxmoxServer
    {
        $id = $request->query('server') ?? $request->input('server_id');

        if ($id) {
            $found = $servers->firstWhere('id', (int) $id);

            if ($found) {
                return $found;
            }

            // fallback fetch bila collection beda scope
            return ProxmoxServer::find($id);
        }

        return $servers->first(fn (ProxmoxServer $s) => $s->is_active) ?? $servers->first();
    }

    /**
     * Import server dari .env bila tabel masih kosong (migrasi dari setup lama).
     */
    protected function bootstrapFromEnvIfEmpty(): void
    {
        if (ProxmoxServer::exists()) {
            return;
        }

        $host = rtrim((string) config('proxmox.host'), '/');
        $password = (string) config('proxmox.password');

        if ($host === '' || $password === '') {
            return;
        }

        if (! str_starts_with($host, 'https://')) {
            return;
        }

        ProxmoxServer::create([
            'name' => 'Proxmox '.parse_url($host, PHP_URL_HOST),
            'host' => $host,
            'user' => (string) config('proxmox.user', 'root@pam'),
            'password' => $password,
            'node' => config('proxmox.node') ?: null,
            'storage' => (string) config('proxmox.storage', 'local-zfs'),
            'bridge' => (string) config('proxmox.bridge', 'vmbr0'),
            'template_vmid' => config('proxmox.template_vmid') ?: null,
            'verify_ssl' => (bool) config('proxmox.verify_ssl', true),
            'is_active' => true,
            'notes' => 'Diimpor otomatis dari .env',
        ]);
    }
}
