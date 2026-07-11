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
                        <code class="text-sm font-mono text-neutral-800">ssh {{ $vmBorrow->vm_username }}&#64;{{ $vmBorrow->host_ip }}</code>
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
            <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm overflow-hidden" x-data="{ action: null, step: null }">
                <div class="px-5 py-4 border-b border-neutral-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h3 class="text-base font-semibold text-neutral-800">Tindakan Admin</h3>
                        <p class="text-sm text-neutral-500">Setujui atau tolak permintaan VM ini.</p>
                    </div>
                    <div class="flex gap-2">
                        <button @click="action = (action === 'approve' && step === 'form') ? null : 'approve'; step = 'confirm'"
                            class="fluent-btn fluent-btn-sm"
                            :class="action === 'approve' ? 'fluent-btn-primary' : 'fluent-btn-ghost'">
                            ✓ ACC / Setujui
                        </button>
                        <button @click="action = (action === 'reject' && step === 'form') ? null : 'reject'; step = 'confirm'"
                            class="fluent-btn fluent-btn-sm"
                            :class="action === 'reject' ? 'fluent-btn-danger' : 'fluent-btn-ghost'">
                            ✕ Tolak
                        </button>
                    </div>
                </div>

                {{-- Step 1: Confirmation --}}
                <div x-show="action && step === 'confirm'" x-transition class="p-5 border-t border-neutral-100">
                    <template x-if="action === 'approve'">
                        <div>
                            <div class="flex items-start gap-3 mb-4">
                                <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <div>
                                    <h4 class="text-base font-semibold text-emerald-800">Konfirmasi Persetujuan</h4>
                                    <p class="text-sm text-neutral-500 mt-0.5">Anda akan menyetujui permintaan VM berikut. Pastikan server sudah disiapkan sebelum melanjutkan.</p>
                                </div>
                            </div>
                            <div class="bg-neutral-50 rounded-xl p-4 mb-4 text-sm space-y-2">
                                <div class="flex justify-between"><span class="text-neutral-500">Peminjam</span><span class="font-medium text-neutral-800">{{ $vmBorrow->user->display_name }}</span></div>
                                <div class="flex justify-between"><span class="text-neutral-500">Email</span><span class="font-medium text-neutral-800">{{ $vmBorrow->user->email }}</span></div>
                                <div class="flex justify-between"><span class="text-neutral-500">OS</span><span class="font-medium text-neutral-800 capitalize">{{ $vmBorrow->os_type }} {{ $vmBorrow->os_version }}</span></div>
                                <div class="flex justify-between"><span class="text-neutral-500">Durasi Diminta</span><span class="font-medium text-neutral-800">{{ $vmBorrow->duration_days }} hari</span></div>
                                <div class="flex justify-between"><span class="text-neutral-500">Keperluan</span><span class="font-medium text-neutral-800">{{ Str::limit($vmBorrow->purpose, 60) }}</span></div>
                            </div>
                            <div class="flex items-center justify-end gap-2">
                                <button type="button" @click="action = null; step = null" class="fluent-btn fluent-btn-ghost fluent-btn-sm">Batal</button>
                                <button type="button" @click="step = 'form'" class="fluent-btn fluent-btn-primary fluent-btn-sm">Lanjut ke Form Kredensial</button>
                            </div>
                        </div>
                    </template>
                    <template x-if="action === 'reject'">
                        <div>
                            <div class="flex items-start gap-3 mb-4">
                                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </div>
                                <div>
                                    <h4 class="text-base font-semibold text-red-800">Konfirmasi Penolakan</h4>
                                    <p class="text-sm text-neutral-500 mt-0.5">Anda akan menolak permintaan VM berikut. Harap berikan alasan yang jelas kepada peminjam.</p>
                                </div>
                            </div>
                            <div class="bg-neutral-50 rounded-xl p-4 mb-4 text-sm space-y-2">
                                <div class="flex justify-between"><span class="text-neutral-500">Peminjam</span><span class="font-medium text-neutral-800">{{ $vmBorrow->user->display_name }}</span></div>
                                <div class="flex justify-between"><span class="text-neutral-500">OS</span><span class="font-medium text-neutral-800 capitalize">{{ $vmBorrow->os_type }} {{ $vmBorrow->os_version }}</span></div>
                                <div class="flex justify-between"><span class="text-neutral-500">Durasi Diminta</span><span class="font-medium text-neutral-800">{{ $vmBorrow->duration_days }} hari</span></div>
                            </div>
                            <div class="flex items-center justify-end gap-2">
                                <button type="button" @click="action = null; step = null" class="fluent-btn fluent-btn-ghost fluent-btn-sm">Batal</button>
                                <button type="button" @click="step = 'form'" class="fluent-btn fluent-btn-danger fluent-btn-sm">Lanjut ke Form Alasan</button>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Step 2: Approve Form (kredensial) --}}
                <div x-show="action === 'approve' && step === 'form'" x-transition class="p-5 border-t border-neutral-100">
                    <h4 class="text-sm font-semibold text-emerald-700 mb-4">Form Kredensial Server</h4>
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
                            <textarea id="admin_notes_approve" name="admin_notes" rows="3" class="fluent-textarea" placeholder="Instruksi tambahan untuk peminjam...">{{ old('admin_notes') }}</textarea>
                            <x-input-error :messages="$errors->get('admin_notes')" />
                        </div>
                        <div class="flex items-center justify-between">
                            <button type="button" @click="step = 'confirm'" class="fluent-btn fluent-btn-ghost fluent-btn-sm">← Kembali</button>
                            <button type="submit" class="fluent-btn fluent-btn-primary fluent-btn-sm" onclick="return confirm('Setujui dan aktifkan VM untuk {{ $vmBorrow->user->display_name }}?')">Setujui & Aktifkan VM</button>
                        </div>
                    </form>
                </div>

                {{-- Step 2: Reject Form (alasan) --}}
                <div x-show="action === 'reject' && step === 'form'" x-transition class="p-5 border-t border-neutral-100">
                    <h4 class="text-sm font-semibold text-red-700 mb-4">Form Alasan Penolakan</h4>
                    <form action="{{ route('vm_borrow.reject', $vmBorrow) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <x-input-label for="admin_notes_reject" value="Alasan Penolakan" />
                            <textarea id="admin_notes_reject" name="admin_notes" rows="4" class="fluent-textarea" required placeholder="Jelaskan alasan penolakan permintaan VM ini...">{{ old('admin_notes') }}</textarea>
                            <x-input-error :messages="$errors->get('admin_notes')" />
                        </div>
                        <div class="flex items-center justify-between">
                            <button type="button" @click="step = 'confirm'" class="fluent-btn fluent-btn-ghost fluent-btn-sm">← Kembali</button>
                            <button type="submit" class="fluent-btn fluent-btn-danger fluent-btn-sm" onclick="return confirm('Tolak permintaan VM dari {{ $vmBorrow->user->display_name }}?')">Tolak Permintaan</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>