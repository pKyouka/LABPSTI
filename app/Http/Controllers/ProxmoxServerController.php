<?php

namespace App\Http\Controllers;

use App\Models\ProxmoxServer;
use App\Services\ProxmoxService;
use Illuminate\Http\Request;

class ProxmoxServerController extends Controller
{
    public function __construct(protected ProxmoxService $proxmox) {}

    public function create(Request $request)
    {
        // Prefill dari Host VM / query (?name=&host=&node=)
        $prefill = [
            'name' => $request->query('name'),
            'host' => $request->query('host'),
            'node' => $request->query('node'),
            'user' => 'root@pam',
            'storage' => 'local-zfs',
            'bridge' => 'vmbr0',
            'verify_ssl' => true,
        ];

        // host query boleh IP saja → wrap ke URL API
        if (! empty($prefill['host']) && ! preg_match('#^https?://#i', $prefill['host'])) {
            $prefill['host'] = 'https://'.ltrim($prefill['host'], '/').':8006';
        }

        return view('proxmox.servers.create', [
            'defaults' => $prefill,
            'prefill' => $prefill,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['is_active'] = $request->boolean('is_active', true);
        $data['verify_ssl'] = $request->boolean('verify_ssl', true);
        $data['host'] = rtrim($data['host'], '/');

        $server = ProxmoxServer::create($data);

        $msg = "Server Proxmox \"{$server->name}\" ditambahkan.";

        // Best-effort sync node → Host VM
        try {
            $multi = ProxmoxServer::count() > 1;
            $synced = $this->proxmox->forServer($server)->syncHosts(prefixServer: $multi);
            $msg .= ' Sync '.count($synced).' node.';
        } catch (\Throwable) {
            // koneksi gagal — biarkan admin test manual
        }

        return redirect()
            ->route('proxmox.index', ['server' => $server->id])
            ->with('success', $msg);
    }

    public function edit(ProxmoxServer $proxmoxServer)
    {
        return view('proxmox.servers.edit', [
            'server' => $proxmoxServer,
        ]);
    }

    public function update(Request $request, ProxmoxServer $proxmoxServer)
    {
        $data = $this->validated($request, $proxmoxServer);
        $data['is_active'] = $request->boolean('is_active', false);
        $data['verify_ssl'] = $request->boolean('verify_ssl', true);
        $data['host'] = rtrim($data['host'], '/');

        // Password kosong = biarkan yang lama
        if (empty($data['password'])) {
            unset($data['password']);
        }

        $proxmoxServer->update($data);

        return redirect()
            ->route('proxmox.index', ['server' => $proxmoxServer->id])
            ->with('success', "Server Proxmox \"{$proxmoxServer->name}\" diperbarui.");
    }

    public function destroy(ProxmoxServer $proxmoxServer)
    {
        $name = $proxmoxServer->name;
        $proxmoxServer->delete();

        return redirect()
            ->route('proxmox.index')
            ->with('success', "Server Proxmox \"{$name}\" dihapus.");
    }

    public function test(ProxmoxServer $proxmoxServer)
    {
        try {
            $result = $this->proxmox->forServer($proxmoxServer)->testConnection();

            return back()->with('success', "Koneksi OK · v{$result['version']} · nodes: ".implode(', ', $result['nodes'] ?? []));
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    protected function validated(Request $request, ?ProxmoxServer $existing = null): array
    {
        $passwordRule = $existing ? 'nullable|string|max:255' : 'required|string|max:255';

        return $request->validate([
            'name' => 'required|string|max:100',
            'host' => 'required|string|max:255|regex:/^https:\/\/.+/i',
            'user' => ['required', 'string', 'max:100', 'regex:/^[A-Za-z0-9@._-]+$/'],
            'password' => $passwordRule,
            'node' => ['nullable', 'string', 'max:64', 'regex:/^[A-Za-z0-9][A-Za-z0-9._-]*$/'],
            'storage' => ['required', 'string', 'max:64', 'regex:/^[A-Za-z0-9][A-Za-z0-9._-]*$/'],
            'bridge' => ['required', 'string', 'max:64', 'regex:/^[A-Za-z0-9][A-Za-z0-9._-]*$/'],
            'template_vmid' => 'nullable|integer|min:100|max:999999999',
            'verify_ssl' => 'boolean',
            'is_active' => 'boolean',
            'notes' => 'nullable|string|max:2000',
        ], [
            'host.regex' => 'Host harus full HTTPS URL, contoh: https://192.168.1.10:8006',
        ]);
    }
}
