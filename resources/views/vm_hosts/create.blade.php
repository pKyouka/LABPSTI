<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('proxmox.index') }}" class="text-neutral-400 hover:text-neutral-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="text-lg font-semibold text-neutral-800">
                {{ __('Tambah Host VM Baru') }}
            </h2>
        </div>
    </x-slot>

    <div class="max-w-2xl">
        <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm p-6">
            <form action="{{ route('vm_hosts.store') }}" method="POST">
                @csrf

                <div class="space-y-5">
                    <!-- Name = hostname / Proxmox node name (match key) -->
                    <div>
                        <x-input-label for="name" :value="__('Hostname')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus placeholder="Harus sama dengan nama node Proxmox, mis. pve" />
                        <p class="mt-1 text-[11px] text-neutral-400">Dipakai match ke node Proxmox. Sync Resource isi otomatis dari cluster.</p>
                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                    </div>

                    <!-- IP Address -->
                    <div>
                        <x-input-label for="ip_address" :value="__('IP Address')" />
                        <x-text-input id="ip_address" name="ip_address" type="text" class="mt-1 block w-full" :value="old('ip_address')" placeholder="Contoh: 192.168.1.100" />
                        <x-input-error :messages="$errors->get('ip_address')" class="mt-1" />
                    </div>

                    <div class="border-t border-neutral-100 pt-5">
                        <h3 class="text-sm font-semibold text-neutral-700 mb-4">Spesifikasi Resource</h3>

                        <!-- CPU -->
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <x-input-label for="cpu_total" :value="__('Total CPU (Cores)')" />
                                <x-text-input id="cpu_total" name="cpu_total" type="number" class="mt-1 block w-full" :value="old('cpu_total', 0)" required min="0" />
                                <x-input-error :messages="$errors->get('cpu_total')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label for="cpu_used" :value="__('CPU Terpakai (Cores)')" />
                                <x-text-input id="cpu_used" name="cpu_used" type="number" class="mt-1 block w-full" :value="old('cpu_used', 0)" required min="0" />
                                <x-input-error :messages="$errors->get('cpu_used')" class="mt-1" />
                            </div>
                        </div>

                        <!-- RAM -->
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <x-input-label for="ram_total" :value="__('Total RAM (MB)')" />
                                <x-text-input id="ram_total" name="ram_total" type="number" class="mt-1 block w-full" :value="old('ram_total', 0)" required min="0" placeholder="Contoh: 16384 untuk 16GB" />
                                <x-input-error :messages="$errors->get('ram_total')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label for="ram_used" :value="__('RAM Terpakai (MB)')" />
                                <x-text-input id="ram_used" name="ram_used" type="number" class="mt-1 block w-full" :value="old('ram_used', 0)" required min="0" />
                                <x-input-error :messages="$errors->get('ram_used')" class="mt-1" />
                            </div>
                        </div>

                        <!-- Storage -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="storage_total" :value="__('Total Storage (GB)')" />
                                <x-text-input id="storage_total" name="storage_total" type="number" class="mt-1 block w-full" :value="old('storage_total', 0)" required min="0" />
                                <x-input-error :messages="$errors->get('storage_total')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label for="storage_used" :value="__('Storage Terpakai (GB)')" />
                                <x-text-input id="storage_used" name="storage_used" type="number" class="mt-1 block w-full" :value="old('storage_used', 0)" required min="0" />
                                <x-input-error :messages="$errors->get('storage_used')" class="mt-1" />
                            </div>
                        </div>
                    </div>

                    <!-- Active Status -->
                    <div class="border-t border-neutral-100 pt-5">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" class="w-4 h-4 rounded border-neutral-300 text-accent focus:ring-accent" @checked(old('is_active', true))>
                            <span class="text-sm font-medium text-neutral-700">Host Aktif</span>
                        </label>
                    </div>

                    <!-- Notes -->
                    <div class="border-t border-neutral-100 pt-5">
                        <x-input-label for="notes" :value="__('Catatan (Opsional)')" />
                        <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full rounded-lg border-neutral-200 shadow-sm focus:border-accent focus:ring focus:ring-accent/20 focus:ring-opacity-50 text-sm text-neutral-700" placeholder="Catatan tambahan untuk host ini...">{{ old('notes') }}</textarea>
                        <x-input-error :messages="$errors->get('notes')" class="mt-1" />
                    </div>

                    <!-- Submit -->
                    <div class="flex items-center gap-3 pt-4 border-t border-neutral-100">
                        <button type="submit" class="fluent-btn fluent-btn-primary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Simpan Host
                        </button>
                        <a href="{{ route('proxmox.index') }}" class="fluent-btn fluent-btn-ghost">Batal</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>