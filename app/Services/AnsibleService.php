<?php

namespace App\Services;

use App\Models\VmBorrowRequest;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class AnsibleService
{
    protected string $ansibleDir;
    protected string $inventoryFile;
    protected string $sshPublicKey;

    public function __construct()
    {
        $this->ansibleDir = base_path('ansible');
        $this->inventoryFile = $this->ansibleDir . '/inventory.yml';
        $this->sshPublicKey = $this->getSshPublicKey();
    }

    /**
     * Jalankan Ansible playbook untuk membuat VM.
     */
    public function createVm(VmBorrowRequest $request): array
    {
        $playbook = $this->ansibleDir . '/playbooks/create-vm.yml';
        $extraVars = $this->buildExtraVars($request);

        $result = $this->runPlaybook($playbook, $extraVars);

        if (!$result['success']) {
            $request->update([
                'admin_notes' => 'Gagal membuat VM otomatis: ' . substr($result['output'], 0, 500),
            ]);
            Log::error('Ansible create VM failed', [
                'request_id' => $request->id,
                'output' => $result['output'],
            ]);
        }

        return $result;
    }

    /**
     * Jalankan Ansible playbook untuk menghapus VM.
     */
    public function destroyVm(VmBorrowRequest $request): array
    {
        // Coba baca VM ID dari file info
        $vmId = $this->getVmIdFromRequest($request);

        $playbook = $this->ansibleDir . '/playbooks/destroy-vm.yml';
        $extraVars = [
            'vm_id' => $vmId,
            'request_id' => $request->id,
        ];

        $result = $this->runPlaybook($playbook, $extraVars);

        if (!$result['success']) {
            Log::error('Ansible destroy VM failed', [
                'request_id' => $request->id,
                'output' => $result['output'],
            ]);
        }

        return $result;
    }

    /**
     * Build extra vars untuk Ansible dari data request.
     */
    protected function buildExtraVars(VmBorrowRequest $request): array
    {
        return [
            'request_id' => $request->id,
            'os_type' => $request->os_type,
            'os_version' => $request->os_version,
            'vm_username' => $request->vm_username ?? 'student',
            'vm_password' => $request->vm_password ?? bin2hex(random_bytes(8)),
            'ssh_public_key' => $this->sshPublicKey,
            'vm_memory_mb' => config('ansible.vm_memory_mb', 2048),
            'vm_cores_count' => config('ansible.vm_cores', 2),
            'vm_disk_size_gb' => config('ansible.vm_disk_gb', 20),
            'vm_network_bridge' => config('ansible.vm_bridge', 'vmbr0'),
            'proxmox_host' => config('ansible.proxmox_host', 'localhost'),
            'proxmox_node_name' => config('ansible.proxmox_node', 'pve'),
            'vm_storage_name' => config('ansible.vm_storage', 'local-zfs'),
            'ssh_key_path' => config('ansible.ssh_key_path', '/root/.ssh/id_rsa'),
        ];
    }

    /**
     * Eksekusi Ansible playbook via CLI.
     */
    protected function runPlaybook(string $playbook, array $extraVars): array
    {
        $inventory = escapeshellarg($this->inventoryFile);
        $playbookEscaped = escapeshellarg($playbook);
        $ansibleCfg = escapeshellarg($this->ansibleDir . '/ansible.cfg');
        $extraVarsJson = escapeshellarg(json_encode($extraVars, JSON_UNESCAPED_SLASHES));

        // Single-level escaping only — do not wrap escapeshellarg() output again.
        $command = 'ANSIBLE_CONFIG='.$ansibleCfg
            .' ansible-playbook -i '.$inventory
            .' '.$playbookEscaped
            .' --extra-vars '.$extraVarsJson
            .' 2>&1';

        $output = [];
        $returnCode = 0;
        exec($command, $output, $returnCode);

        $outputStr = implode("\n", $output);

        // Parse output untuk mendapatkan IP address
        $ipAddress = null;
        foreach ($output as $line) {
            if (preg_match('/Host:\s*(\d+\.\d+\.\d+\.\d+)/', $line, $m)) {
                $ipAddress = $m[1];
                break;
            }
        }

        return [
            'success' => $returnCode === 0,
            'output' => $outputStr,
            'ip_address' => $ipAddress,
            'return_code' => $returnCode,
        ];
    }

    /**
     * Ambil VM ID dari request (dari file info atau kolom host_ip).
     */
    protected function getVmIdFromRequest(VmBorrowRequest $request): ?string
    {
        $infoFile = "/root/vm-borrow-{$request->id}.env";
        if (file_exists($infoFile)) {
            $content = file_get_contents($infoFile);
            if (preg_match('/VM_ID=(\d+)/', $content, $m)) {
                return $m[1];
            }
        }
        return null;
    }

    /**
     * Ambil SSH public key untuk disisipkan ke VM.
     */
    protected function getSshPublicKey(): string
    {
        $keyPath = config('ansible.ssh_public_key_path', '/root/.ssh/id_rsa.pub');
        if (file_exists($keyPath)) {
            return trim(file_get_contents($keyPath));
        }

        // Fallback: generate key pair jika belum ada
        $keyDir = dirname($keyPath);
        if (!is_dir($keyDir)) {
            mkdir($keyDir, 0700, true);
        }

        $keyPathEscaped = escapeshellarg($keyPath);
        exec("ssh-keygen -t ed25519 -f {$keyPathEscaped} -N '' -q", $output, $rc);
        if ($rc === 0 && file_exists($keyPath)) {
            return trim(file_get_contents($keyPath));
        }

        return '';
    }
}
