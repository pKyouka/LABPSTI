<?php

namespace App\Services;

use App\Models\ProxmoxServer;
use App\Models\VmHost;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class ProxmoxService
{
    protected string $baseUrl = '';
    protected string $user = '';
    protected string $password = '';
    protected bool $verifySsl = false;
    protected int $timeout = 30;

    protected ?string $ticket = null;
    protected ?string $csrf = null;

    protected ?ProxmoxServer $server = null;

    /**
     * Defaults global (timeout, fallback create-VM specs).
     * Kredensial per-server lewat forServer() / fromConfigFallback().
     */
    public function __construct()
    {
        $this->timeout = (int) config('proxmox.timeout', 30);
    }

    public function forServer(ProxmoxServer $server): static
    {
        $clone = clone $this;
        $clone->server = $server;
        $clone->baseUrl = $server->baseUrl();
        $clone->user = $server->user;
        $clone->password = (string) $server->password;
        $clone->verifySsl = (bool) $server->verify_ssl;
        $clone->ticket = null;
        $clone->csrf = null;

        return $clone;
    }

    /**
     * Fallback .env bila belum ada row di DB (migrasi lama).
     */
    public function fromConfigFallback(): static
    {
        $clone = clone $this;
        $clone->server = null;
        $clone->baseUrl = (string) config('proxmox.host');
        $clone->user = (string) config('proxmox.user');
        $clone->password = (string) config('proxmox.password');
        $clone->verifySsl = (bool) config('proxmox.verify_ssl');
        $clone->ticket = null;
        $clone->csrf = null;

        return $clone;
    }

    public function server(): ?ProxmoxServer
    {
        return $this->server;
    }

    public function defaultNode(): ?string
    {
        return $this->server?->node ?: config('proxmox.node');
    }

    public function defaultStorage(): string
    {
        return $this->server?->storage ?: (string) config('proxmox.storage', 'local-zfs');
    }

    public function defaultBridge(): string
    {
        return $this->server?->bridge ?: (string) config('proxmox.bridge', 'vmbr0');
    }

    public function defaultTemplateVmid(): ?int
    {
        $id = $this->server?->template_vmid ?: config('proxmox.template_vmid');

        return $id ? (int) $id : null;
    }

    public function isConfigured(): bool
    {
        return $this->baseUrl !== '' && $this->user !== '' && $this->password !== '';
    }

    /**
     * Cek koneksi + auth. Return info version/node bila sukses.
     */
    public function testConnection(): array
    {
        $this->authenticate(force: true);

        $version = $this->get('version');
        $nodes = $this->getNodes();

        return [
            'ok' => true,
            'version' => $version['version'] ?? null,
            'release' => $version['release'] ?? null,
            'nodes' => collect($nodes)->pluck('node')->values()->all(),
        ];
    }

    public function getNodes(): array
    {
        return $this->get('nodes') ?? [];
    }

    public function getNodeStatus(string $node): array
    {
        return $this->get("nodes/{$node}/status") ?? [];
    }

    /**
     * Semua VM + CT di cluster (type=vm).
     */
    public function getClusterVms(): array
    {
        $resources = $this->get('cluster/resources', ['type' => 'vm']) ?? [];

        return collect($resources)
            ->map(fn (array $r) => [
                'vmid' => $r['vmid'] ?? null,
                'name' => $r['name'] ?? ('vm-'.($r['vmid'] ?? '?')),
                'node' => $r['node'] ?? null,
                'type' => $r['type'] ?? 'qemu', // qemu | lxc
                'status' => $r['status'] ?? 'unknown',
                'cpu' => $r['cpu'] ?? 0,
                'cpus' => $r['maxcpu'] ?? ($r['cpus'] ?? 0),
                'mem' => $r['mem'] ?? 0,
                'maxmem' => $r['maxmem'] ?? 0,
                'disk' => $r['disk'] ?? 0,
                'maxdisk' => $r['maxdisk'] ?? 0,
                'uptime' => $r['uptime'] ?? 0,
                'template' => (bool) ($r['template'] ?? 0),
            ])
            ->sortBy('vmid')
            ->values()
            ->all();
    }

    public function getNextId(): int
    {
        $id = $this->get('cluster/nextid');

        if ($id === null || $id === '') {
            throw new RuntimeException('Gagal mendapatkan next VMID dari Proxmox.');
        }

        return (int) $id;
    }

    public function getVmStatus(string $node, int $vmid, string $type = 'qemu'): array
    {
        $path = $type === 'lxc'
            ? "nodes/{$node}/lxc/{$vmid}/status/current"
            : "nodes/{$node}/qemu/{$vmid}/status/current";

        return $this->get($path) ?? [];
    }

    public function getVmConfig(string $node, int $vmid, string $type = 'qemu'): array
    {
        $path = $type === 'lxc'
            ? "nodes/{$node}/lxc/{$vmid}/config"
            : "nodes/{$node}/qemu/{$vmid}/config";

        return $this->get($path) ?? [];
    }

    /**
     * Create blank QEMU VM (tanpa OS). Admin pasang ISO manual, atau gunakan cloneTemplate().
     */
    public function createVm(array $params): array
    {
        $node = $params['node'] ?? $this->defaultNode();
        $vmid = $params['vmid'] ?? $this->getNextId();
        $name = $params['name'] ?? ('vm-'.$vmid);
        $cores = (int) ($params['cores'] ?? config('proxmox.defaults.cores'));
        $memory = (int) ($params['memory'] ?? config('proxmox.defaults.memory'));
        $diskGb = (int) ($params['disk'] ?? config('proxmox.defaults.disk'));
        $storage = $params['storage'] ?? $this->defaultStorage();
        $bridge = $params['bridge'] ?? $this->defaultBridge();

        $body = [
            'vmid' => $vmid,
            'name' => $name,
            'cores' => $cores,
            'memory' => $memory,
            'scsihw' => 'virtio-scsi-pci',
            'scsi0' => "{$storage}:{$diskGb}",
            'net0' => "virtio,bridge={$bridge}",
            'ostype' => $params['ostype'] ?? 'l26',
            'agent' => $params['agent'] ?? '1',
        ];

        if (! empty($params['iso'])) {
            // format: local:iso/debian-12.iso,media=cdrom
            $body['ide2'] = $params['iso'].',media=cdrom';
            $body['boot'] = 'order=ide2;scsi0';
        } else {
            $body['boot'] = 'order=scsi0';
        }

        if (! empty($params['description'])) {
            $body['description'] = $params['description'];
        }

        $upid = $this->post("nodes/{$node}/qemu", $body);

        return [
            'vmid' => $vmid,
            'node' => $node,
            'name' => $name,
            'upid' => $upid,
        ];
    }

    /**
     * Clone dari template VMID.
     */
    public function cloneTemplate(array $params): array
    {
        $node = $params['node'] ?? $this->defaultNode();
        $templateId = (int) ($params['template_vmid'] ?? $this->defaultTemplateVmid());

        if (! $templateId) {
            throw new RuntimeException('Template VMID belum dikonfigurasi di server Proxmox ini.');
        }

        $vmid = $params['vmid'] ?? $this->getNextId();
        $name = $params['name'] ?? ('vm-'.$vmid);

        $body = [
            'newid' => $vmid,
            'name' => $name,
            'full' => $params['full'] ?? 1,
            'target' => $node,
        ];

        if (! empty($params['description'])) {
            $body['description'] = $params['description'];
        }

        $storage = $params['storage'] ?? $this->defaultStorage();
        if ($storage) {
            $body['storage'] = $storage;
        }

        $upid = $this->post("nodes/{$node}/qemu/{$templateId}/clone", $body);

        // Resize resources after clone (async — best-effort)
        $cores = $params['cores'] ?? null;
        $memory = $params['memory'] ?? null;
        if ($cores || $memory) {
            $config = array_filter([
                'cores' => $cores,
                'memory' => $memory,
            ]);
            try {
                $this->put("nodes/{$node}/qemu/{$vmid}/config", $config);
            } catch (\Throwable $e) {
                Log::warning('Proxmox clone resize skipped', ['error' => $e->getMessage()]);
            }
        }

        return [
            'vmid' => $vmid,
            'node' => $node,
            'name' => $name,
            'upid' => $upid,
            'from_template' => $templateId,
        ];
    }

    public function startVm(string $node, int $vmid, string $type = 'qemu'): mixed
    {
        return $this->post("nodes/{$node}/{$type}/{$vmid}/status/start");
    }

    public function stopVm(string $node, int $vmid, string $type = 'qemu'): mixed
    {
        return $this->post("nodes/{$node}/{$type}/{$vmid}/status/stop");
    }

    public function shutdownVm(string $node, int $vmid, string $type = 'qemu'): mixed
    {
        return $this->post("nodes/{$node}/{$type}/{$vmid}/status/shutdown");
    }

    public function rebootVm(string $node, int $vmid, string $type = 'qemu'): mixed
    {
        return $this->post("nodes/{$node}/{$type}/{$vmid}/status/reboot");
    }

    /**
     * Hapus VM. Stop dulu bila masih running (force).
     */
    public function deleteVm(string $node, int $vmid, string $type = 'qemu', bool $purge = true): mixed
    {
        try {
            $status = $this->getVmStatus($node, $vmid, $type);
            if (($status['status'] ?? '') === 'running') {
                $this->stopVm($node, $vmid, $type);
                usleep(500_000);
            }
        } catch (\Throwable) {
            // lanjut delete
        }

        $query = $purge ? ['purge' => 1, 'destroy-unreferenced-disks' => 1] : [];

        return $this->delete("nodes/{$node}/{$type}/{$vmid}", $query);
    }

    /**
     * Sinkron node Proxmox → tabel vm_hosts (resource live).
     * Hostname prefix: "{serverName}/{node}" bila multi-server, else node name.
     */
    public function syncHosts(bool $prefixServer = false): array
    {
        $nodes = $this->getNodes();
        $synced = [];
        $serverLabel = $this->server?->name;

        foreach ($nodes as $nodeInfo) {
            $nodeName = $nodeInfo['node'] ?? null;
            if (! $nodeName) {
                continue;
            }

            $hostKey = ($prefixServer && $serverLabel)
                ? $serverLabel.'/'.$nodeName
                : $nodeName;

            $status = $this->getNodeStatus($nodeName);

            $cpuTotal = (int) ($status['cpuinfo']['cpus'] ?? $nodeInfo['maxcpu'] ?? 0);
            // maxmem bytes → MB
            $ramTotalMb = (int) round(($status['memory']['total'] ?? $nodeInfo['maxmem'] ?? 0) / 1024 / 1024);
            $ramUsedMb = (int) round(($status['memory']['used'] ?? $nodeInfo['mem'] ?? 0) / 1024 / 1024);

            // rootfs storage
            $storageTotalGb = (int) round(($status['rootfs']['total'] ?? 0) / 1024 / 1024 / 1024);
            $storageUsedGb = (int) round(($status['rootfs']['used'] ?? 0) / 1024 / 1024 / 1024);

            $cpuUsed = $this->estimateCpuUsed($nodeName);

            $host = VmHost::updateOrCreate(
                ['name' => $hostKey],
                [
                    'ip_address' => $this->extractNodeIp() ?? null,
                    'cpu_total' => $cpuTotal,
                    'cpu_used' => min($cpuUsed, $cpuTotal),
                    'ram_total' => $ramTotalMb,
                    'ram_used' => min($ramUsedMb, $ramTotalMb),
                    'storage_total' => $storageTotalGb,
                    'storage_used' => min($storageUsedGb, $storageTotalGb),
                    'is_active' => ($nodeInfo['status'] ?? '') === 'online',
                    'notes' => 'Disinkron dari Proxmox'
                        .($serverLabel ? " [{$serverLabel}]" : '')
                        .' '.now()->toDateTimeString(),
                ]
            );

            $synced[] = $host;
        }

        return $synced;
    }

    protected function estimateCpuUsed(string $node): int
    {
        $vms = collect($this->getClusterVms())
            ->where('node', $node)
            ->where('template', false)
            ->where('status', 'running');

        // Jumlah core yang di-allocate ke VM running
        return (int) $vms->sum('cpus');
    }

    protected function extractNodeIp(): ?string
    {
        // Ambil host dari URL config
        $host = parse_url($this->baseUrl, PHP_URL_HOST);

        return $host ?: null;
    }

    /* ------------------------------------------------------------------
     | HTTP layer
     * ----------------------------------------------------------------*/

    protected function authenticate(bool $force = false): void
    {
        if (! $force && $this->ticket && $this->csrf) {
            return;
        }

        if (! $force) {
            $cached = Cache::get($this->cacheKey());
            if (is_array($cached) && ! empty($cached['ticket']) && ! empty($cached['csrf'])) {
                $this->ticket = $cached['ticket'];
                $this->csrf = $cached['csrf'];

                return;
            }
        }

        if (! $this->isConfigured()) {
            throw new RuntimeException('Proxmox belum dikonfigurasi. Tambah server di menu Host & Proxmox.');
        }

        $response = $this->rawClient()
            ->asForm()
            ->post($this->baseUrl.'/api2/json/access/ticket', [
                'username' => $this->user,
                'password' => $this->password,
            ]);

        if (! $response->successful()) {
            Log::error('Proxmox auth failed', [
                'host' => $this->baseUrl,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new RuntimeException('Autentikasi Proxmox gagal (HTTP '.$response->status().'). Cek user/password di server.');
        }

        $data = $response->json('data');
        if (empty($data['ticket']) || empty($data['CSRFPreventionToken'])) {
            throw new RuntimeException('Respons auth Proxmox tidak valid.');
        }

        $this->ticket = $data['ticket'];
        $this->csrf = $data['CSRFPreventionToken'];

        // Ticket Proxmox ~2 jam; cache 90 menit
        Cache::put($this->cacheKey(), [
            'ticket' => $this->ticket,
            'csrf' => $this->csrf,
        ], now()->addMinutes(90));
    }

    protected function get(string $path, array $query = []): mixed
    {
        return $this->request('get', $path, $query);
    }

    protected function post(string $path, array $data = []): mixed
    {
        return $this->request('post', $path, $data);
    }

    protected function put(string $path, array $data = []): mixed
    {
        return $this->request('put', $path, $data);
    }

    protected function delete(string $path, array $query = []): mixed
    {
        return $this->request('delete', $path, $query);
    }

    protected function request(string $method, string $path, array $payload = []): mixed
    {
        $this->authenticate();

        $url = $this->baseUrl.'/api2/json/'.ltrim($path, '/');

        $client = $this->authedClient();

        $response = match (strtolower($method)) {
            'get' => $client->get($url, $payload),
            'post' => $client->asForm()->post($url, $payload),
            'put' => $client->asForm()->put($url, $payload),
            'delete' => $client->delete($url, $payload),
            default => throw new RuntimeException("HTTP method tidak didukung: {$method}"),
        };

        // Ticket expired → reauth sekali
        if ($response->status() === 401) {
            $this->authenticate(force: true);
            $client = $this->authedClient();
            $response = match (strtolower($method)) {
                'get' => $client->get($url, $payload),
                'post' => $client->asForm()->post($url, $payload),
                'put' => $client->asForm()->put($url, $payload),
                'delete' => $client->delete($url, $payload),
            };
        }

        if (! $response->successful()) {
            $msg = $response->json('errors')
                ?? $response->json('message')
                ?? $response->body();

            if (is_array($msg)) {
                $msg = json_encode($msg);
            }

            Log::error('Proxmox API error', [
                'method' => $method,
                'path' => $path,
                'host' => $this->baseUrl,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new RuntimeException('Proxmox API error ('.$response->status().'): '.$msg);
        }

        return $response->json('data');
    }

    protected function rawClient(): PendingRequest
    {
        $client = Http::timeout($this->timeout)
            ->acceptJson();

        if (! $this->verifySsl) {
            $client = $client->withoutVerifying();
        }

        return $client;
    }

    protected function authedClient(): PendingRequest
    {
        // Cookie header lebih andal daripada cookie jar untuk API ticket Proxmox
        return $this->rawClient()
            ->withHeaders([
                'CSRFPreventionToken' => $this->csrf,
                'Cookie' => 'PVEAuthCookie='.$this->ticket,
            ]);
    }

    protected function cacheKey(): string
    {
        $id = $this->server?->id ?? 'env';

        return 'proxmox.ticket.'.$id.'.'.md5($this->baseUrl.'|'.$this->user);
    }
}
