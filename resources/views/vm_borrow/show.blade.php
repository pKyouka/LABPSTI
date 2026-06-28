<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-4">
            <div>
                <h2 class="font-display font-bold text-2xl text-slate-900 leading-tight">
                    Detail Permintaan VM
                </h2>
                <p class="text-sm text-slate-500 mt-0.5">
                    #{{ $vmBorrow->id }} &mdash; Diajukan {{ $vmBorrow->created_at->diffForHumans() }}
                </p>
            </div>
            <a href="{{ route('vm_borrow.index') }}" class="inline-flex items-center px-5 py-2 rounded-xl bg-slate-900 text-white text-xs font-black uppercase tracking-widest hover:bg-slate-800 transition">
                ← Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="panel-shell space-y-6">
            @if(session('success'))
                <div class="panel-card">
                    <div class="panel-card-body">
                        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl" role="alert">
                            {{ session('success') }}
                        </div>
                    </div>
                </div>
            @endif

            {{-- Status Banner --}}
            @if($vmBorrow->status === 'approved')
                <div class="panel-card overflow-hidden border-emerald-200">
                    <div class="panel-card-head bg-emerald-50/50 border-b-emerald-100">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-[11px] font-black uppercase tracking-widest text-emerald-700">✓ Disetujui</span>
                            <span class="text-sm text-emerald-700 font-semibold">VM Anda telah diaktifkan. Gunakan informasi di bawah untuk mengakses.</span>
                        </div>
                    </div>
                    <div class="panel-card-body">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                            <div class="rounded-2xl bg-slate-900 px-5 py-4 text-white">
                                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Host IP</p>
                                <p class="text-xl font-black font-mono tracking-wide">{{ $vmBorrow->host_ip }}</p>
                            </div>
                            <div class="rounded-2xl bg-slate-900 px-5 py-4 text-white">
                                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Username</p>
                                <p class="text-xl font-black font-mono">{{ $vmBorrow->vm_username }}</p>
                            </div>
                            <div class="rounded-2xl bg-slate-900 px-5 py-4 text-white">
                                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Password</p>
                                <p class="text-xl font-black font-mono">{{ $vmBorrow->vm_password }}</p>
                            </div>
                        </div>
                        <div class="mt-4 flex flex-wrap gap-4 text-sm text-slate-600">
                            <span>
                                <span class="font-black text-slate-800">Aktif:</span>
                                {{ $vmBorrow->start_date->format('d M Y') }}
                            </span>
                            <span>—</span>
                            <span>
                                <span class="font-black text-slate-800">Berakhir:</span>
                                {{ $vmBorrow->end_date?->format('d M Y') ?? '-' }}
                            </span>
                            <span>
                                <span class="font-black text-slate-800">Durasi:</span>
                                {{ $vmBorrow->duration_days }} hari
                            </span>
                        </div>
                        <div class="mt-4 rounded-2xl bg-cyan-50 border border-cyan-200 px-4 py-3">
                            <p class="text-xs font-black uppercase tracking-widest text-cyan-700 mb-1">Cara Akses (SSH)</p>
                            <code class="text-sm font-mono text-slate-800">ssh {{ $vmBorrow->vm_username }} @ {{ $vmBorrow->host_ip }}</code>
                        </div>
                        @if($vmBorrow->admin_notes)
                            <div class="mt-4">
                                <p class="text-xs font-black uppercase tracking-widest text-slate-500 mb-1">Catatan Admin</p>
                                <p class="text-sm text-slate-700">{{ $vmBorrow->admin_notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @elseif($vmBorrow->status === 'rejected')
                <div class="panel-card overflow-hidden">
                    <div class="panel-card-head">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center rounded-full bg-rose-100 px-3 py-1 text-[11px] font-black uppercase tracking-widest text-rose-700">✕ Ditolak</span>
                            <span class="text-sm text-rose-700 font-semibold">Permintaan VM Anda ditolak.</span>
                        </div>
                    </div>
                    @if($vmBorrow->admin_notes)
                        <div class="panel-card-body">
                            <p class="text-xs font-black uppercase tracking-widest text-slate-500 mb-2">Alasan Penolakan</p>
                            <p class="text-sm text-slate-700 bg-rose-50 border border-rose-200 rounded-2xl px-4 py-3">{{ $vmBorrow->admin_notes }}</p>
                        </div>
                    @endif
                </div>
            @else
                <div class="panel-card overflow-hidden">
                    <div class="panel-card-head">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-[11px] font-black uppercase tracking-widest text-amber-700">⏳ Menunggu ACC</span>
                            <span class="text-sm text-amber-700 font-semibold">Permintaan Anda sedang menunggu persetujuan admin.</span>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Request Details --}}
            <div class="panel-card overflow-hidden">
                <div class="panel-card-head">
                    <h3 class="text-lg font-black text-slate-900">Informasi Permintaan</h3>
                </div>
                <div class="panel-card-body">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        @if(Auth::user()->isAdmin())
                            <div>
                                <dt class="text-[11px] font-black uppercase tracking-widest text-slate-500 mb-1">Peminjam</dt>
                                <dd class="text-sm font-semibold text-slate-800">{{ $vmBorrow->user->display_name }}</dd>
                                <dd class="text-xs text-slate-500">{{ $vmBorrow->user->email }}</dd>
                            </div>
                        @endif
                        <div>
                            <dt class="text-[11px] font-black uppercase tracking-widest text-slate-500 mb-1">Sistem Operasi</dt>
                            <dd class="text-sm font-semibold text-slate-800 capitalize">{{ $vmBorrow->os_type }} {{ $vmBorrow->os_version }}</dd>
                        </div>
                        <div>
                            <dt class="text-[11px] font-black uppercase tracking-widest text-slate-500 mb-1">Tanggal Mulai</dt>
                            <dd class="text-sm font-semibold text-slate-800">{{ $vmBorrow->start_date->format('d M Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-[11px] font-black uppercase tracking-widest text-slate-500 mb-1">Durasi Diminta</dt>
                            <dd class="text-sm font-semibold text-slate-800">{{ $vmBorrow->duration_days }} hari</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-[11px] font-black uppercase tracking-widest text-slate-500 mb-1">Keperluan</dt>
                            <dd class="text-sm text-slate-700 whitespace-pre-wrap">{{ $vmBorrow->purpose }}</dd>
                        </div>
                        <div>
                            <dt class="text-[11px] font-black uppercase tracking-widest text-slate-500 mb-1">Tanggal Pengajuan</dt>
                            <dd class="text-sm font-semibold text-slate-800">{{ $vmBorrow->created_at->format('d M Y, H:i') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            {{-- Admin Actions (only for pending + admin) --}}
            @if(Auth::user()->isAdmin() && $vmBorrow->status === 'pending')
                <div class="panel-card overflow-hidden" x-data="{ action: null }">
                    <div class="panel-card-head flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <h3 class="text-lg font-black text-slate-900">Tindakan Admin</h3>
                            <p class="text-sm text-slate-500">Setujui atau tolak permintaan VM ini.</p>
                        </div>
                        <div class="flex gap-3">
                            <button @click="action = action === 'approve' ? null : 'approve'"
                                class="inline-flex items-center px-5 py-2 rounded-xl text-xs font-black uppercase tracking-widest transition"
                                :class="action === 'approve' ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-200' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100'">
                                ✓ ACC / Setujui
                            </button>
                            <button @click="action = action === 'reject' ? null : 'reject'"
                                class="inline-flex items-center px-5 py-2 rounded-xl text-xs font-black uppercase tracking-widest transition"
                                :class="action === 'reject' ? 'bg-rose-600 text-white shadow-lg shadow-rose-200' : 'bg-rose-50 text-rose-700 hover:bg-rose-100'">
                                ✕ Tolak
                            </button>
                        </div>
                    </div>

                    {{-- Approve Form --}}
                    <div x-show="action === 'approve'" x-transition class="panel-card-body border-t border-slate-100">
                        <h4 class="text-sm font-black uppercase tracking-widest text-emerald-700 mb-4">Form Persetujuan VM</h4>
                        <form action="{{ route('vm_borrow.approve', $vmBorrow) }}" method="POST" class="space-y-5">
                            @csrf
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <x-input-label for="host_ip" value="Host IP / Alamat Server" />
                                    <x-text-input id="host_ip" class="panel-input" type="text" name="host_ip"
                                        :value="old('host_ip')" placeholder="192.168.1.100" required />
                                    <x-input-error :messages="$errors->get('host_ip')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="duration_days" value="Durasi Aktual (hari)" />
                                    <x-text-input id="duration_days" class="panel-input" type="number"
                                        name="duration_days"
                                        :value="old('duration_days', $vmBorrow->duration_days)"
                                        min="1" max="365" required />
                                    <x-input-error :messages="$errors->get('duration_days')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="vm_username" value="Username VM" />
                                    <x-text-input id="vm_username" class="panel-input" type="text" name="vm_username"
                                        :value="old('vm_username')" placeholder="student" required />
                                    <x-input-error :messages="$errors->get('vm_username')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="vm_password" value="Password VM" />
                                    <x-text-input id="vm_password" class="panel-input" type="text" name="vm_password"
                                        :value="old('vm_password')" placeholder="••••••••" required />
                                    <x-input-error :messages="$errors->get('vm_password')" class="mt-2" />
                                </div>
                            </div>
                            <div>
                                <x-input-label for="admin_notes_approve" value="Catatan Admin (opsional)" />
                                <textarea id="admin_notes_approve" name="admin_notes" rows="3" class="panel-textarea"
                                    placeholder="Instruksi tambahan atau catatan untuk peminjam...">{{ old('admin_notes') }}</textarea>
                                <x-input-error :messages="$errors->get('admin_notes')" class="mt-2" />
                            </div>
                            <div class="flex items-center justify-end gap-3">
                                <button type="button" @click="action = null" class="text-sm text-slate-500 hover:text-slate-800 font-bold">Batal</button>
                                <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-emerald-600 text-white font-black rounded-xl hover:bg-emerald-700 transition shadow-lg shadow-emerald-200 uppercase tracking-widest text-[10px]">
                                    Setujui & Aktifkan VM
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- Reject Form --}}
                    <div x-show="action === 'reject'" x-transition class="panel-card-body border-t border-slate-100">
                        <h4 class="text-sm font-black uppercase tracking-widest text-rose-700 mb-4">Form Penolakan</h4>
                        <form action="{{ route('vm_borrow.reject', $vmBorrow) }}" method="POST" class="space-y-5">
                            @csrf
                            <div>
                                <x-input-label for="admin_notes_reject" value="Alasan Penolakan" />
                                <textarea id="admin_notes_reject" name="admin_notes" rows="4" class="panel-textarea" required
                                    placeholder="Jelaskan alasan penolakan permintaan VM ini...">{{ old('admin_notes') }}</textarea>
                                <x-input-error :messages="$errors->get('admin_notes')" class="mt-2" />
                            </div>
                            <div class="flex items-center justify-end gap-3">
                                <button type="button" @click="action = null" class="text-sm text-slate-500 hover:text-slate-800 font-bold">Batal</button>
                                <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-rose-600 text-white font-black rounded-xl hover:bg-rose-700 transition shadow-lg shadow-rose-200 uppercase tracking-widest text-[10px]"
                                    onclick="return confirm('Yakin ingin menolak permintaan VM ini?')">
                                    Tolak Permintaan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>