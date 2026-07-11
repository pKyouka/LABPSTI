<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-4">
            <div>
                <h2 class="text-lg font-semibold text-neutral-800">Detail Permintaan VM</h2>
                <p class="text-sm text-neutral-500 mt-0.5">#{{ $vmBorrow->id }} &mdash; Diajukan {{ $vmBorrow->created_at->diffForHumans() }}</p>
            </div>
            <a href="{{ route('vm_borrow.index') }}" class="fluent-btn fluent-btn-secondary fluent-btn-sm">← Kembali</a>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if(session('success'))
            <div class="px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium rounded-xl">{{ session('success') }}</div>
        @endif

        @if($vmBorrow->status === 'approved')
            <div class="bg-white rounded-2xl border border-emerald-200 shadow-fluent-sm overflow-hidden">
                <div class="px-5 py-4 bg-emerald-50/50 border-b border-emerald-100 flex items-center gap-3">
                    <span class="fluent-badge fluent-badge-success">✓ Disetujui</span>
                    <span class="text-sm text-emerald-700 font-medium">VM Anda telah diaktifkan. Gunakan informasi di bawah untuk mengakses.</span>
                </div>
                <div class="p-5 space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="rounded-xl bg-neutral-800 px-5 py-4 text-white">
                            <p class="text-[10px] font-semibold uppercase tracking-wider text-neutral-400 mb-1">Host IP</p>
                            <p class="text-lg font-mono font-semibold tracking-wide">{{ $vmBorrow->host_ip }}</p>
                        </div>
                        <div class="rounded-xl bg-neutral-800 px-5 py-4 text-white">
                            <p class="text-[10px] font-semibold uppercase tracking-wider text-neutral-400 mb-1">Username</p>
                            <p class="text-lg font-mono font-semibold">{{ $vmBorrow->vm_username }}</p>
                        </div>
                        <div class="rounded-xl bg-neutral-800 px-5 py-4 text-white">
                            <p class="text-[10px] font-semibold uppercase tracking-wider text-neutral-400 mb-1">Password</p>
                            <p class="text-lg font-mono font-semibold">{{ $vmBorrow->vm_password }}</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-x-4 gap-y-1 text-sm text-neutral-600">
                        <span><span class="font-medium text-neutral-800">Aktif:</span> {{ $vmBorrow->start_date->format('d M Y') }}</span>
                        <span class="text-neutral-300">—</span>
                        <span><span class="font-medium text-neutral-800">Berakhir:</span> {{ $vmBorrow->end_date?->format('d M Y') ?? '-' }}</span>
                        <span><span class="font-medium text-neutral-800">Durasi:</span> {{ $vmBorrow->duration_days }} hari</span>
                    </div>
                    <div class="rounded-xl bg-accent-light border border-accent-200 px-4 py-3">
                        <p class="text-xs font-semibold uppercase tracking-wider text-accent mb-1">Cara Akses (SSH)</p>
                        <code class="text-sm font-mono text-neutral-800">ssh {{ $vmBorrow->vm_username }}@{{ $vmBorrow->host_ip }}</code>
                    </div>
                    @if($vmBorrow->admin_notes)
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-neutral-500 mb-1">Catatan Admin</p>
                            <p class="text-sm text-neutral-700">{{ $vmBorrow->admin_notes }}</p>
                        </div>
                    @endif
                    @include('vm_borrow.partials._reset_history')
                    @include('vm_borrow.partials._reset_user_actions')
                    @include('vm_borrow.partials._reset_admin_panel')
                </div>
            </div>
        @elseif($vmBorrow->status === 'rejected')
            <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm overflow-hidden">
                <div class="px-5 py-4 flex items-center gap-3">
                    <span class="fluent-badge fluent-badge-danger">✕ Ditolak</span>
                    <span class="text-sm text-red-700 font-medium">Permintaan VM Anda ditolak.</span>
                </div>
                @if($vmBorrow->admin_notes)
                    <div class="px-5 pb-5">
                        <p class="text-xs font-semibold uppercase tracking-wider text-neutral-500 mb-2">Alasan Penolakan</p>
                        <p class="text-sm text-neutral-700 bg-red-50 border border-red-200 rounded-xl px-4 py-3">{{ $vmBorrow->admin_notes }}</p>
                    </div>
                @endif
            </div>
        @else
            <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm overflow-hidden">
                <div class="px-5 py-4 flex items-center gap-3">
                    <span class="fluent-badge fluent-badge-warning">⏳ Menunggu ACC</span>
                    <span class="text-sm text-amber-700 font-medium">Permintaan Anda sedang menunggu persetujuan admin.</span>
                </div>
            </div>
        @endif

        {{-- Request Details --}}
        <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-neutral-100">
                <h3 class="text-base font-semibold text-neutral-800">Informasi Permintaan</h3>
            </div>
            <div class="p-5">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    @if(Auth::user()->isAdmin())
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wider text-neutral-500 mb-1">Peminjam</dt>
                            <dd class="text-sm font-medium text-neutral-800">{{ $vmBorrow->user->display_name }}</dd>
                            <dd class="text-xs text-neutral-500">{{ $vmBorrow->user->email }}</dd>
                        </div>
                    @endif
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-neutral-500 mb-1">Sistem Operasi</dt>
                        <dd class="text-sm font-medium text-neutral-800 capitalize">{{ $vmBorrow->os_type }} {{ $vmBorrow->os_version }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-neutral-500 mb-1">Tanggal Mulai</dt>
                        <dd class="text-sm font-medium text-neutral-800">{{ $vmBorrow->start_date->format('d M Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-neutral-500 mb-1">Durasi Diminta</dt>
                        <dd class="text-sm font-medium text-neutral-800">{{ $vmBorrow->duration_days }} hari</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-neutral-500 mb-1">Keperluan</dt>
                        <dd class="text-sm text-neutral-700 whitespace-pre-wrap">{{ $vmBorrow->purpose }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-neutral-500 mb-1">Tanggal Pengajuan</dt>
                        <dd class="text-sm font-medium text-neutral-800">{{ $vmBorrow->created_at->format('d M Y, H:i') }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- Admin Actions --}}
        @if(Auth::user()->isAdmin() && $vmBorrow->status === 'pending')
            <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm overflow-hidden" x-data="{ action: null }">
                <div class="px-5 py-4 border-b border-neutral-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h3 class="text-base font-semibold text-neutral-800">Tindakan Admin</h3>
                        <p class="text-sm text-neutral-500">Setujui atau tolak permintaan VM ini.</p>
                    </div>
                    <div class="flex gap-2">
                        <button @click="action = action === 'approve' ? null : 'approve'"
                            class="fluent-btn fluent-btn-sm"
                            :class="action === 'approve' ? 'fluent-btn-primary' : 'fluent-btn-ghost'">
                            ✓ ACC / Setujui
                        </button>
                        <button @click="action = action === 'reject' ? null : 'reject'"
                            class="fluent-btn fluent-btn-sm"
                            :class="action === 'reject' ? 'fluent-btn-danger' : 'fluent-btn-ghost'">
                            ✕ Tolak
                        </button>
                    </div>
                </div>

                <div x-show="action === 'approve'" x-transition class="p-5 border-t border-neutral-100">
                    <h4 class="text-sm font-semibold text-emerald-700 mb-4">Form Persetujuan VM</h4>
                    <form action="{{ route('vm_borrow.approve', $vmBorrow) }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="host_ip" value="Host IP / Alamat Server" />
                                <x-text-input id="host_ip" type="text" name="host_ip" :value="old('host_ip')" placeholder="192.168.1.100" required />
                                <x-input-error :messages="$errors->get('host_ip')" />
                            </div>
                            <div>
                                <x-input-label for="duration_days" value="Durasi Aktual (hari)" />
                                <x-text-input id="duration_days" type="number" name="duration_days" :value="old('duration_days', $vmBorrow->duration_days)" min="1" max="365" required />
                                <x-input-error :messages="$errors->get('duration_days')" />
                            </div>
                            <div>
                                <x-input-label for="vm_username" value="Username VM" />
                                <x-text-input id="vm_username" type="text" name="vm_username" :value="old('vm_username')" placeholder="student" required />
                                <x-input-error :messages="$errors->get('vm_username')" />
                            </div>
                            <div>
                                <x-input-label for="vm_password" value="Password VM" />
                                <x-text-input id="vm_password" type="text" name="vm_password" :value="old('vm_password')" placeholder="••••••••" required />
                                <x-input-error :messages="$errors->get('vm_password')" />
                            </div>
                        </div>
                        <div>
                            <x-input-label for="admin_notes_approve" value="Catatan Admin (opsional)" />
                            <textarea id="admin_notes_approve" name="admin_notes" rows="3" class="fluent-textarea" placeholder="Instruksi tambahan...">{{ old('admin_notes') }}</textarea>
                            <x-input-error :messages="$errors->get('admin_notes')" />
                        </div>
                        <div class="flex items-center justify-end gap-2">
                            <button type="button" @click="action = null" class="fluent-btn fluent-btn-ghost fluent-btn-sm">Batal</button>
                            <button type="submit" class="fluent-btn fluent-btn-primary fluent-btn-sm">Setujui & Aktifkan VM</button>
                        </div>
                    </form>
                </div>

                <div x-show="action === 'reject'" x-transition class="p-5 border-t border-neutral-100">
                    <h4 class="text-sm font-semibold text-red-700 mb-4">Form Penolakan</h4>
                    <form action="{{ route('vm_borrow.reject', $vmBorrow) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <x-input-label for="admin_notes_reject" value="Alasan Penolakan" />
                            <textarea id="admin_notes_reject" name="admin_notes" rows="4" class="fluent-textarea" required placeholder="Jelaskan alasan penolakan...">{{ old('admin_notes') }}</textarea>
                            <x-input-error :messages="$errors->get('admin_notes')" />
                        </div>
                        <div class="flex items-center justify-end gap-2">
                            <button type="button" @click="action = null" class="fluent-btn fluent-btn-ghost fluent-btn-sm">Batal</button>
                            <button type="submit" class="fluent-btn fluent-btn-danger fluent-btn-sm" onclick="return confirm('Yakin ingin menolak permintaan VM ini?')">Tolak Permintaan</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>