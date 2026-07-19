<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('proxmox.index') }}" class="text-neutral-400 hover:text-neutral-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="text-lg font-semibold text-neutral-800">Buat Virtual Machine</h2>
        </div>
    </x-slot>

    <div class="max-w-2xl">
        @if($error)
            <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 text-sm font-medium rounded-xl">{{ $error }}</div>
        @endif
        @if(session('error'))
            <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 text-sm font-medium rounded-xl">{{ session('error') }}</div>
        @endif

        <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm p-6">
            <form action="{{ route('proxmox.store') }}" method="POST" x-data="{ mode: '{{ old('mode', $templateVmid ? 'clone' : 'blank') }}' }">
                @csrf

                <div class="space-y-5">
                    @if(isset($servers) && $servers->count() > 1)
                    <div>
                        <x-input-label for="server_id" value="Server Proxmox" />
                        <select id="server_id" name="server_id" class="mt-1 block w-full rounded-lg border-neutral-200 shadow-sm focus:border-accent focus:ring focus:ring-accent/20 text-sm" required
                            onchange="window.location='{{ route('proxmox.create') }}?server='+this.value">
                            @foreach($servers as $srv)
                                <option value="{{ $srv->id }}" @selected(old('server_id', $activeServer?->id) == $srv->id)>{{ $srv->name }} · {{ $srv->host }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('server_id')" class="mt-1" />
                    </div>
                    @elseif($activeServer)
                        <input type="hidden" name="server_id" value="{{ old('server_id', $activeServer->id) }}">
                        <p class="text-[12px] text-neutral-500">Server: <span class="font-medium text-neutral-700">{{ $activeServer->name }}</span> <span class="font-mono text-neutral-400">{{ $activeServer->host }}</span></p>
                    @endif

                    {{-- Mode --}}
                    <div>
                        <x-input-label :value="__('Mode Pembuatan')" />
                        <div class="mt-2 grid grid-cols-2 gap-3">
                            <label class="cursor-pointer">
                                <input type="radio" name="mode" value="clone" class="sr-only peer" x-model="mode" @checked(old('mode', $templateVmid ? 'clone' : 'blank') === 'clone')>
                                <div class="rounded-xl border border-neutral-200 p-3 peer-checked:border-accent peer-checked:bg-accent/5 transition">
                                    <p class="text-sm font-semibold text-neutral-800">Clone Template</p>
                                    <p class="text-[11px] text-neutral-400 mt-0.5">Salin dari template VM yang sudah siap</p>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="mode" value="blank" class="sr-only peer" x-model="mode" @checked(old('mode', $templateVmid ? 'clone' : 'blank') === 'blank')>
                                <div class="rounded-xl border border-neutral-200 p-3 peer-checked:border-accent peer-checked:bg-accent/5 transition">
                                    <p class="text-sm font-semibold text-neutral-800">Blank VM</p>
                                    <p class="text-[11px] text-neutral-400 mt-0.5">VM kosong + disk, pasang ISO manual</p>
                                </div>
                            </label>
                        </div>
                        <x-input-error :messages="$errors->get('mode')" class="mt-1" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="node" :value="__('Node')" />
                            <select id="node" name="node" class="mt-1 block w-full rounded-lg border-neutral-200 shadow-sm focus:border-accent focus:ring focus:ring-accent/20 text-sm" required>
                                @forelse($nodes as $n)
                                    <option value="{{ $n }}" @selected(old('node', $defaultNode) === $n)>{{ $n }}</option>
                                @empty
                                    <option value="{{ $defaultNode }}">{{ $defaultNode }}</option>
                                @endforelse
                            </select>
                            <x-input-error :messages="$errors->get('node')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="vmid" :value="__('VMID (opsional)')" />
                            <x-text-input id="vmid" name="vmid" type="number" class="mt-1 block w-full" :value="old('vmid', $nextId)" min="100" placeholder="Auto" />
                            <p class="text-[11px] text-neutral-400 mt-1">Next free: {{ $nextId ?? '—' }}</p>
                            <x-input-error :messages="$errors->get('vmid')" class="mt-1" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="name" :value="__('Nama VM')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus placeholder="contoh: lab-debian-01" pattern="[A-Za-z0-9][A-Za-z0-9.\-_]*" />
                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                    </div>

                    <div x-show="mode === 'clone'" x-cloak>
                        <x-input-label for="template_vmid" :value="__('Template VMID')" />
                        <x-text-input id="template_vmid" name="template_vmid" type="number" class="mt-1 block w-full" :value="old('template_vmid', $templateVmid)" min="100" placeholder="ID template di Proxmox" />
                        <p class="text-[11px] text-neutral-400 mt-1">Default server: {{ $templateVmid ?: 'belum diset di server' }}</p>
                        <x-input-error :messages="$errors->get('template_vmid')" class="mt-1" />
                    </div>

                    <div class="border-t border-neutral-100 pt-5">
                        <h3 class="text-sm font-semibold text-neutral-700 mb-4">Resource</h3>
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <x-input-label for="cores" :value="__('CPU Cores')" />
                                <x-text-input id="cores" name="cores" type="number" class="mt-1 block w-full" :value="old('cores', $defaults['cores'])" required min="1" max="128" />
                                <x-input-error :messages="$errors->get('cores')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label for="memory" :value="__('RAM (MB)')" />
                                <x-text-input id="memory" name="memory" type="number" class="mt-1 block w-full" :value="old('memory', $defaults['memory'])" required min="512" />
                                <x-input-error :messages="$errors->get('memory')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label for="disk" :value="__('Disk (GB)')" />
                                <x-text-input id="disk" name="disk" type="number" class="mt-1 block w-full" :value="old('disk', $defaults['disk'])" required min="1" />
                                <p class="text-[11px] text-neutral-400 mt-1" x-show="mode === 'clone'" x-cloak>Clone: disk ikut template</p>
                                <x-input-error :messages="$errors->get('disk')" class="mt-1" />
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="storage" :value="__('Storage')" />
                            <x-text-input id="storage" name="storage" type="text" class="mt-1 block w-full" :value="old('storage', $storage)" placeholder="local-zfs" />
                            <x-input-error :messages="$errors->get('storage')" class="mt-1" />
                        </div>
                        <div x-show="mode === 'blank'" x-cloak>
                            <x-input-label for="bridge" :value="__('Bridge')" />
                            <x-text-input id="bridge" name="bridge" type="text" class="mt-1 block w-full" :value="old('bridge', $bridge)" placeholder="vmbr0" />
                            <x-input-error :messages="$errors->get('bridge')" class="mt-1" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="description" :value="__('Deskripsi (opsional)')" />
                        <textarea id="description" name="description" rows="2" class="mt-1 block w-full rounded-lg border-neutral-200 shadow-sm focus:border-accent focus:ring focus:ring-accent/20 text-sm text-neutral-700" placeholder="Catatan VM...">{{ old('description') }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-1" />
                    </div>

                    <div x-show="mode === 'clone'" x-cloak>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="start_after" value="1" class="w-4 h-4 rounded border-neutral-300 text-accent focus:ring-accent" @checked(old('start_after'))>
                            <span class="text-sm font-medium text-neutral-700">Start VM setelah clone</span>
                        </label>
                    </div>

                    <div class="rounded-xl bg-amber-50 border border-amber-100 px-4 py-3 text-[12px] text-amber-800" x-show="mode === 'blank'" x-cloak>
                        Blank VM belum punya OS. Setelah dibuat, pasang ISO lewat Proxmox UI atau cloud-init template.
                    </div>

                    <div class="flex items-center gap-3 pt-4 border-t border-neutral-100">
                        <button type="submit" class="fluent-btn fluent-btn-primary" @disabled($error && empty($nodes))>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Buat VM
                        </button>
                        <a href="{{ route('proxmox.index') }}" class="fluent-btn fluent-btn-ghost">Batal</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
