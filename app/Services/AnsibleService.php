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
            $request->forceFill([
                'admin_notes' => 'Gagal membuat VM otomatis: '.substr($result['output'], 0, 500),
            ])->save();
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
        if ($vmId === null || ! ctype_digit((string) $vmId)) {
            return [
                'success' => false,
                'output' => 'VM ID tidak ditemukan atau tidak valid.',
                'ip_address' => null,
                'return_code' => 1,
            ];
        }

        $extraVars = [
            'vm_id' => (int) $vmId,
            'request_id' => (int) $request->id,
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
        $username = $this->sanitizeVmUsername($request->vm_username ?? 'student');
        $password = $request->vm_password ?: bin2hex(random_bytes(12));

        if (strlen($password) < 8 || strlen($password) > 128) {
            throw new RuntimeException('Password VM tidak valid untuk provisioning.');
        }

        return [
            'request_id' => (int) $request->id,
            'os_type' => in_array($request->os_type, ['debian', 'ubuntu'], true) ? $request->os_type : 'ubuntu',
            'os_version' => preg_replace('/[^0-9.]/', '', (string) $request->os_version) ?: '24.04',
            'vm_username' => $username,
            'vm_password' => $password,
            'ssh_public_key' => $this->sshPublicKey,
            'vm_memory_mb' => (int) config('ansible.vm_memory_mb', 2048),
            'vm_cores_count' => (int) config('ansible.vm_cores', 2),
            'vm_disk_size_gb' => (int) config('ansible.vm_disk_gb', 20),
            'vm_network_bridge' => preg_replace('/[^A-Za-z0-9._-]/', '', (string) config('ansible.vm_bridge', 'vmbr0')) ?: 'vmbr0',
            'proxmox_host' => config('ansible.proxmox_host', 'localhost'),
            'proxmox_node_name' => preg_replace('/[^A-Za-z0-9._-]/', '', (string) config('ansible.proxmox_node', 'pve')) ?: 'pve',
            'vm_storage_name' => preg_replace('/[^A-Za-z0-9._-]/', '', (string) config('ansible.vm_storage', 'local-zfs')) ?: 'local-zfs',
            'ssh_key_path' => config('ansible.ssh_key_path', '/root/.ssh/id_rsa'),
        ];
    }

    /**
     * Username Linux aman (huruf kecil, angka, underscore/dash).
     */
    protected function sanitizeVmUsername(?string $username): string
    {
        $username = strtolower(trim((string) $username));
        if ($username === '' || ! preg_match('/^[a-z_][a-z0-9_-]{0,31}$/', $username)) {
            return 'student';
        }

        return $username;
    }

    /**
     * Eksekusi Ansible playbook via CLI (proc_open, tanpa shell interpolation).
     */
    protected function runPlaybook(string $playbook, array $extraVars): array
    {
        if (! is_file($playbook) || ! str_starts_with(realpath($playbook) ?: '', realpath($this->ansibleDir) ?: $this->ansibleDir)) {
            throw new RuntimeException('Playbook Ansible tidak valid.');
        }

        $env = array_merge($_ENV, [
            'ANSIBLE_CONFIG' => $this->ansibleDir.DIRECTORY_SEPARATOR.'ansible.cfg',
            'PATH' => getenv('PATH') ?: '/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin',
        ]);

        $cmd = [
            'ansible-playbook',
            '-i', $this->inventoryFile,
            $playbook,
            '--extra-vars', json_encode($extraVars, JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR),
        ];

        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $process = proc_open($cmd, $descriptors, $pipes, $this->ansibleDir, $env);

        if (! is_resource($process)) {
            throw new RuntimeException('Gagal menjalankan ansible-playbook.');
        }

        fclose($pipes[0]);
        $stdout = stream_get_contents($pipes[1]) ?: '';
        $stderr = stream_get_contents($pipes[2]) ?: '';
        fclose($pipes[1]);
        fclose($pipes[2]);
        $returnCode = proc_close($process);

        $outputStr = trim($stdout.($stderr !== '' ? "\n".$stderr : ''));
        $output = $outputStr === '' ? [] : preg_split("/\r\n|\n|\r/", $outputStr);

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

        $cmd = ['ssh-keygen', '-t', 'ed25519', '-f', $keyPath, '-N', '', '-q'];
        $descriptors = [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
        $process = proc_open($cmd, $descriptors, $pipes, null, null);

        if (is_resource($process)) {
            fclose($pipes[0]);
            stream_get_contents($pipes[1]);
            stream_get_contents($pipes[2]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            $rc = proc_close($process);
            if ($rc === 0 && file_exists($keyPath)) {
                return trim((string) file_get_contents($keyPath));
            }
        }

        return '';
    }
}
