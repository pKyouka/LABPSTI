<?php

namespace App\Http\Controllers;

use App\Services\ProxmoxService;
use Illuminate\Http\Request;
use RuntimeException;

class ProxmoxController extends Controller
{
    public function __construct(protected ProxmoxService $proxmox) {}

    public function index()
    {
        $configured = $this->proxmox->isConfigured();
        $error = null;
        $connection = null;
        $nodes = [];
        $vms = [];

        if ($configured) {
            try {
                $connection = $this->proxmox->testConnection();
                $nodes = collect($this->proxmox->getNodes())->map(function (array $n) {
                    $status = [];
                    try {
                        $status = $this->proxmox->getNodeStatus($n['node']);
                    } catch (\Throwable) {
                        // node offline
                    }

                    $memTotal = $status['memory']['total'] ?? $n['maxmem'] ?? 0;
                    $memUsed = $status['memory']['used'] ?? $n['mem'] ?? 0;
                    $cpu = $status['cpu'] ?? $n['cpu'] ?? 0;

                    return [
                        'name' => $n['node'],
                        'status' => $n['status'] ?? 'unknown',
                        'cpu_pct' => round(((float) $cpu) * 100, 1),
                        'cpus' => $status['cpuinfo']['cpus'] ?? $n['maxcpu'] ?? 0,
                        'mem_used_gb' => round($memUsed / 1024 / 1024 / 1024, 1),
                        'mem_total_gb' => round($memTotal / 1024 / 1024 / 1024, 1),
                        'mem_pct' => $memTotal > 0 ? round(($memUsed / $memTotal) * 100, 1) : 0,
                        'uptime' => $status['uptime'] ?? $n['uptime'] ?? 0,
                    ];
                })->all();

                $vms = $this->proxmox->getClusterVms();
            } catch (RuntimeException $e) {
                $error = $e->getMessage();
            } catch (\Throwable $e) {
                $error = 'Gagal terhubung ke Proxmox: '.$e->getMessage();
            }
        }

        return view('proxmox.index', compact('configured', 'connection', 'nodes', 'vms', 'error'));
    }

    public function create()
    {
        $nodes = [];
        $nextId = null;
        $error = null;
        $templateVmid = config('proxmox.template_vmid');

        try {
            $nodes = collect($this->proxmox->getNodes())->pluck('node')->all();
            $nextId = $this->proxmox->getNextId();
        } catch (\Throwable $e) {
            $error = $e->getMessage();
        }

        return view('proxmox.create', [
            'nodes' => $nodes,
            'nextId' => $nextId,
            'error' => $error,
            'templateVmid' => $templateVmid,
            'defaults' => config('proxmox.defaults'),
            'defaultNode' => config('proxmox.node'),
            'storage' => config('proxmox.storage'),
            'bridge' => config('proxmox.bridge'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
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

        try {
            if ($data['mode'] === 'clone') {
                $result = $this->proxmox->cloneTemplate([
                    'node' => $data['node'],
                    'vmid' => $data['vmid'] ?? null,
                    'name' => $data['name'],
                    'cores' => $data['cores'],
                    'memory' => $data['memory'],
                    'storage' => $data['storage'] ?? config('proxmox.storage'),
                    'template_vmid' => $data['template_vmid'] ?? config('proxmox.template_vmid'),
                    'description' => $data['description'] ?? null,
                ]);
            } else {
                $result = $this->proxmox->createVm([
                    'node' => $data['node'],
                    'vmid' => $data['vmid'] ?? null,
                    'name' => $data['name'],
                    'cores' => $data['cores'],
                    'memory' => $data['memory'],
                    'disk' => $data['disk'],
                    'storage' => $data['storage'] ?? config('proxmox.storage'),
                    'bridge' => $data['bridge'] ?? config('proxmox.bridge'),
                    'description' => $data['description'] ?? null,
                ]);
            }

            if ($request->boolean('start_after') && $data['mode'] === 'clone') {
                try {
                    $this->proxmox->startVm($result['node'], $result['vmid']);
                } catch (\Throwable) {
                    // clone mungkin masih running task
                }
            }

            return redirect()
                ->route('proxmox.index')
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
            'node' => 'required|string|max:64',
            'vmid' => 'required|integer|min:100',
            'type' => 'required|in:qemu,lxc',
        ]);

        try {
            $this->proxmox->deleteVm($data['node'], $data['vmid'], $data['type']);

            return back()->with('success', "VM/CT {$data['vmid']} dihapus dari node {$data['node']}.");
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function syncHosts()
    {
        try {
            $hosts = $this->proxmox->syncHosts();
            $count = count($hosts);

            return redirect()
                ->route('vm_hosts.index')
                ->with('success', "Berhasil sinkron {$count} node Proxmox ke Host VM.");
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function test()
    {
        try {
            $result = $this->proxmox->testConnection();

            return response()->json(['ok' => true, 'data' => $result]);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 422);
        }
    }

    protected function powerAction(Request $request, string $action)
    {
        $data = $request->validate([
            'node' => 'required|string|max:64',
            'vmid' => 'required|integer|min:100',
            'type' => 'required|in:qemu,lxc',
        ]);

        try {
            match ($action) {
                'start' => $this->proxmox->startVm($data['node'], $data['vmid'], $data['type']),
                'stop' => $this->proxmox->stopVm($data['node'], $data['vmid'], $data['type']),
                'shutdown' => $this->proxmox->shutdownVm($data['node'], $data['vmid'], $data['type']),
                'reboot' => $this->proxmox->rebootVm($data['node'], $data['vmid'], $data['type']),
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
}
