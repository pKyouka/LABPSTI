@php
    $isEdit = (bool) $server;
    $d = $defaults ?? [];
@endphp
<div class="space-y-5">
    <div>
        <x-input-label for="name" value="Nama / Label" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $server->name ?? ($d['name'] ?? ''))" required autofocus placeholder="Contoh: Cluster Lab A" />
        <p class="mt-1 text-[11px] text-neutral-400">Label admin, bebas. Dipakai di selector &amp; prefix sync multi-server.</p>
        <x-input-error :messages="$errors->get('name')" class="mt-1" />
    </div>

    <div>
        <x-input-label for="host" value="Host URL" />
        <x-text-input id="host" name="host" type="text" class="mt-1 block w-full font-mono text-sm" :value="old('host', $server->host ?? ($d['host'] ?? ''))" required placeholder="https://192.168.1.10:8006" />
        <p class="mt-1 text-[11px] text-neutral-400">Full URL termasuk port (biasanya 8006).</p>
        <x-input-error :messages="$errors->get('host')" class="mt-1" />
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <x-input-label for="user" value="User" />
            <x-text-input id="user" name="user" type="text" class="mt-1 block w-full" :value="old('user', $server->user ?? ($d['user'] ?? 'root@pam'))" required placeholder="root@pam" />
            <x-input-error :messages="$errors->get('user')" class="mt-1" />
        </div>
        <div>
            <x-input-label for="password" :value="$isEdit ? __('Password (kosongkan = biarkan)') : __('Password')" />
            {{-- native input: @if di dalam x-text-input bisa bikin field tak ter-render --}}
            <input
                id="password"
                name="password"
                type="password"
                class="fluent-input mt-1 block w-full"
                autocomplete="new-password"
                placeholder="Password Proxmox"
                @if(! $isEdit) required @endif
            />
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>
    </div>

    <div class="border-t border-neutral-100 pt-5">
        <h3 class="text-sm font-semibold text-neutral-700 mb-4">Default resource</h3>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <x-input-label for="node" value="Node default" />
                <x-text-input id="node" name="node" type="text" class="mt-1 block w-full" :value="old('node', $server->node ?? ($d['node'] ?? ''))" placeholder="pveDev1" />
                <p class="mt-1 text-[11px] text-neutral-400">Opsional. Nama node default saat buat VM.</p>
                <x-input-error :messages="$errors->get('node')" class="mt-1" />
            </div>
            <div>
                <x-input-label for="template_vmid" value="Template VMID" />
                <x-text-input id="template_vmid" name="template_vmid" type="number" class="mt-1 block w-full" :value="old('template_vmid', $server->template_vmid ?? '')" min="100" placeholder="900" />
                <x-input-error :messages="$errors->get('template_vmid')" class="mt-1" />
            </div>
            <div>
                <x-input-label for="storage" value="Storage" />
                <x-text-input id="storage" name="storage" type="text" class="mt-1 block w-full" :value="old('storage', $server->storage ?? ($defaults['storage'] ?? 'local-zfs'))" required />
                <x-input-error :messages="$errors->get('storage')" class="mt-1" />
            </div>
            <div>
                <x-input-label for="bridge" value="Bridge" />
                <x-text-input id="bridge" name="bridge" type="text" class="mt-1 block w-full" :value="old('bridge', $server->bridge ?? ($defaults['bridge'] ?? 'vmbr0'))" required />
                <x-input-error :messages="$errors->get('bridge')" class="mt-1" />
            </div>
        </div>
    </div>

    <div>
        <x-input-label for="notes" value="Catatan" />
        <textarea id="notes" name="notes" rows="2" class="mt-1 block w-full rounded-lg border-neutral-200 shadow-sm focus:border-accent focus:ring focus:ring-accent/20 text-sm text-neutral-700" placeholder="Opsional...">{{ old('notes', $server->notes ?? '') }}</textarea>
        <x-input-error :messages="$errors->get('notes')" class="mt-1" />
    </div>

    <div class="flex flex-wrap gap-5">
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="is_active" value="1" class="w-4 h-4 rounded border-neutral-300 text-accent focus:ring-accent"
                @checked(old('is_active', $server->is_active ?? true))>
            <span class="text-sm font-medium text-neutral-700">Aktif</span>
        </label>
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="verify_ssl" value="1" class="w-4 h-4 rounded border-neutral-300 text-accent focus:ring-accent"
                @checked(old('verify_ssl', $server->verify_ssl ?? ($defaults['verify_ssl'] ?? false)))>
            <span class="text-sm font-medium text-neutral-700">Verify SSL</span>
        </label>
    </div>

    <div class="flex items-center gap-3 pt-4 border-t border-neutral-100">
        <button type="submit" class="fluent-btn fluent-btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ $isEdit ? 'Perbarui Server' : 'Simpan Server' }}
        </button>
        <a href="{{ route('proxmox.index', $isEdit ? ['server' => $server->id] : []) }}" class="fluent-btn fluent-btn-ghost">Batal</a>
    </div>
</div>
