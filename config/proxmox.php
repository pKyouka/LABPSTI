<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Proxmox VE — defaults global
    |--------------------------------------------------------------------------
    |
    | Kredensial multi-server disimpan di tabel proxmox_servers (CRUD admin).
    | Nilai di bawah hanya fallback create-VM specs + timeout HTTP.
    | Legacy env PROXMOX_HOST/USER/PASSWORD masih dibaca ProxmoxService::fromConfigFallback()
    | bila diperlukan script lama, tapi UI pakai DB.
    |
    */

    'host' => rtrim(env('PROXMOX_HOST', ''), '/'),

    'user' => env('PROXMOX_USER', 'root@pam'),

    'password' => env('PROXMOX_PASSWORD', ''),

    'node' => env('PROXMOX_NODE', 'pve'),

    'storage' => env('PROXMOX_STORAGE', 'local-zfs'),

    'bridge' => env('PROXMOX_BRIDGE', 'vmbr0'),

    // Default true. Set PROXMOX_VERIFY_SSL=false only for lab certs (self-signed).
    'verify_ssl' => filter_var(env('PROXMOX_VERIFY_SSL', true), FILTER_VALIDATE_BOOLEAN),

    'template_vmid' => env('PROXMOX_TEMPLATE_VMID'),

    'defaults' => [
        'cores'  => (int) env('PROXMOX_DEFAULT_CORES', 2),
        'memory' => (int) env('PROXMOX_DEFAULT_MEMORY', 2048), // MB
        'disk'   => (int) env('PROXMOX_DEFAULT_DISK', 20),     // GB
    ],

    'timeout' => (int) env('PROXMOX_TIMEOUT', 30),

];
