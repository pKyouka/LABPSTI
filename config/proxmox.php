<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Proxmox VE API
    |--------------------------------------------------------------------------
    |
    | Koneksi langsung ke Proxmox REST API (ticket auth, root@pam).
    | Host harus full URL termasuk port, contoh: https://192.168.1.10:8006
    |
    */

    'host' => rtrim(env('PROXMOX_HOST', 'https://127.0.0.1:8006'), '/'),

    'user' => env('PROXMOX_USER', 'root@pam'),

    'password' => env('PROXMOX_PASSWORD', ''),

    // Node default bila cluster hanya 1 node
    'node' => env('PROXMOX_NODE', 'pve'),

    'storage' => env('PROXMOX_STORAGE', 'local-zfs'),

    'bridge' => env('PROXMOX_BRIDGE', 'vmbr0'),

    // Self-signed cert di lab biasanya true=false
    'verify_ssl' => filter_var(env('PROXMOX_VERIFY_SSL', false), FILTER_VALIDATE_BOOLEAN),

    // VMID template untuk clone (kosong = create blank + ISO manual)
    'template_vmid' => env('PROXMOX_TEMPLATE_VMID'),

    // Default specs create VM
    'defaults' => [
        'cores'  => (int) env('PROXMOX_DEFAULT_CORES', 2),
        'memory' => (int) env('PROXMOX_DEFAULT_MEMORY', 2048), // MB
        'disk'   => (int) env('PROXMOX_DEFAULT_DISK', 20),     // GB
    ],

    // Timeout HTTP (detik)
    'timeout' => (int) env('PROXMOX_TIMEOUT', 30),

];
