<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Ansible Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk Ansible provisioning VM menggunakan community-scripts.
    |
    */

    // Proxmox host (bisa diisi IP Proxmox server)
    'proxmox_host' => env('ANSIBLE_PROXMOX_HOST', 'localhost'),

    // Proxmox node name
    'proxmox_node' => env('ANSIBLE_PROXMOX_NODE', 'pve'),

    // Storage untuk VM
    'vm_storage' => env('ANSIBLE_VM_STORAGE', 'local-zfs'),

    // Default VM specs
    'vm_memory_mb' => env('ANSIBLE_VM_MEMORY', 2048),
    'vm_cores' => env('ANSIBLE_VM_CORES', 2),
    'vm_disk_gb' => env('ANSIBLE_VM_DISK', 20),
    'vm_bridge' => env('ANSIBLE_VM_BRIDGE', 'vmbr0'),

    // SSH key paths on the server running Ansible
    'ssh_key_path' => env('ANSIBLE_SSH_KEY_PATH', '/root/.ssh/id_rsa'),
    'ssh_public_key_path' => env('ANSIBLE_SSH_PUBLIC_KEY_PATH', '/root/.ssh/id_rsa.pub'),

    // Apakah Ansible auto-provision aktif?
    'auto_provision' => env('ANSIBLE_AUTO_PROVISION', true),

    // Path ke ansible-playbook binary
    'ansible_binary' => env('ANSIBLE_BINARY', '/usr/bin/ansible-playbook'),
];
