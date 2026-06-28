<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="font-display font-bold text-2xl text-slate-900 leading-tight">
                {{ __('Ajukan Peminjaman Virtual Machine') }}
            </h2>
            <p class="text-sm text-slate-500 max-w-2xl">
                Isi formulir di bawah untuk mengajukan permintaan VM. Permintaan akan diproses oleh admin dan Anda akan mendapat notifikasi.
            </p>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="panel-shell">
            @if(session('success'))
                <div class="panel-card mb-6">
                    <div class="panel-card-body">
                        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl" role="alert">
                            {{ session('success') }}
                        </div>
                    </div>
                </div>
            @endif

            <div class="panel-card overflow-hidden">
                <div class="panel-card-head">
                    <h3 class="text-lg font-black text-slate-900">Detail Permintaan VM</h3>
                    <p class="text-sm text-slate-500 mt-0.5">Pilih sistem operasi, versi, dan isi informasi peminjaman.</p>
                </div>
                <div class="panel-card-body">
                    <form action="{{ route('vm_borrow.store') }}" method="POST" x-data="vmBorrowForm()" class="space-y-6">
                        @csrf

                        {{-- OS Type Cards --}}
                        <div>
                            <x-input-label value="Sistem Operasi" />
                            <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="os_type" value="debian" class="peer sr-only"
                                        {{ old('os_type', 'debian') === 'debian' ? 'checked' : '' }}
                                        @change="osType = 'debian'; osVersion = ''">
                                    <div class="relative overflow-hidden rounded-3xl border-2 border-slate-200 bg-white p-5 text-left shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl peer-checked:border-cyan-500 peer-checked:ring-4 peer-checked:ring-cyan-100 peer-checked:shadow-lg peer-checked:shadow-cyan-100 hover:border-cyan-300">
                                        <div class="absolute inset-0 bg-gradient-to-br from-cyan-50 via-white to-blue-50 opacity-0 peer-checked:opacity-100 transition-opacity duration-300"></div>
                                        <div class="relative z-10 flex h-full flex-col gap-3">
                                            <div class="inline-flex items-center rounded-full bg-slate-900 px-3 py-1.5 text-[10px] font-black uppercase tracking-widest text-white w-fit">
                                                Debian
                                            </div>
                                            <div>
                                                <h3 class="text-xl font-black text-slate-900">Debian GNU/Linux</h3>
                                                <p class="mt-1 text-sm text-slate-500">Distro stabil berbasis Debian. Tersedia versi 10, 11, 12.</p>
                                            </div>
                                            <div class="flex items-center gap-2 text-xs font-black uppercase tracking-widest text-cyan-700">
                                                <span x-text="osType === 'debian' ? '✓ Dipilih' : 'Pilih →'"></span>
                                            </div>
                                        </div>
                                    </div>
                                </label>

                                <label class="relative cursor-pointer">
                                    <input type="radio" name="os_type" value="ubuntu" class="peer sr-only"
                                        {{ old('os_type') === 'ubuntu' ? 'checked' : '' }}
                                        @change="osType = 'ubuntu'; osVersion = ''">
                                    <div class="relative overflow-hidden rounded-3xl border-2 border-slate-200 bg-white p-5 text-left shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl peer-checked:border-cyan-500 peer-checked:ring-4 peer-checked:ring-cyan-100 peer-checked:shadow-lg peer-checked:shadow-cyan-100 hover:border-cyan-300">
                                        <div class="absolute inset-0 bg-gradient-to-br from-cyan-50 via-white to-blue-50 opacity-0 peer-checked:opacity-100 transition-opacity duration-300"></div>
                                        <div class="relative z-10 flex h-full flex-col gap-3">
                                            <div class="inline-flex items-center rounded-full bg-slate-900 px-3 py-1.5 text-[10px] font-black uppercase tracking-widest text-white w-fit">
                                                Ubuntu
                                            </div>
                                            <div>
                                                <h3 class="text-xl font-black text-slate-900">Ubuntu Linux</h3>
                                                <p class="mt-1 text-sm text-slate-500">Distro populer dari Canonical. Tersedia versi 22.04 hingga 24.04.</p>
                                            </div>
                                            <div class="flex items-center gap-2 text-xs font-black uppercase tracking-widest text-cyan-700">
                                                <span x-text="osType === 'ubuntu' ? '✓ Dipilih' : 'Pilih →'"></span>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            <x-input-error :messages="$errors->get('os_type')" class="mt-2" />
                        </div>

                        {{-- OS Version --}}
                        <div>
                            <x-input-label for="os_version" value="Versi OS" />
                            <div class="mt-3">
                                <template x-if="osType === 'debian'">
                                    <div class="grid grid-cols-3 sm:grid-cols-4 gap-3">
                                        @foreach($osVersions['debian'] as $ver)
                                            <label class="relative cursor-pointer">
                                                <input type="radio" name="os_version" value="{{ $ver }}" class="peer sr-only"
                                                    {{ old('os_version') === $ver ? 'checked' : '' }}
                                                    @change="osVersion = '{{ $ver }}'">
                                                <div class="flex flex-col items-center justify-center rounded-2xl border-2 border-slate-200 bg-white px-4 py-3 text-center transition peer-checked:border-cyan-500 peer-checked:bg-cyan-50 peer-checked:text-cyan-700 hover:border-slate-300">
                                                    <span class="text-sm font-black text-slate-800 peer-checked:text-cyan-700">Debian</span>
                                                    <span class="text-xl font-black">{{ $ver }}</span>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </template>
                                <template x-if="osType === 'ubuntu'">
                                    <div class="grid grid-cols-3 sm:grid-cols-5 gap-3">
                                        @foreach($osVersions['ubuntu'] as $ver)
                                            <label class="relative cursor-pointer">
                                                <input type="radio" name="os_version" value="{{ $ver }}" class="peer sr-only"
                                                    {{ old('os_version') === $ver ? 'checked' : '' }}
                                                    @change="osVersion = '{{ $ver }}'">
                                                <div class="flex flex-col items-center justify-center rounded-2xl border-2 border-slate-200 bg-white px-4 py-3 text-center transition peer-checked:border-cyan-500 peer-checked:bg-cyan-50 hover:border-slate-300">
                                                    <span class="text-xs font-black text-slate-500">Ubuntu</span>
                                                    <span class="text-base font-black text-slate-800">{{ $ver }}</span>
                                                    @if(in_array($ver, ['22.04', '24.04']))
                                                        <span class="mt-1 inline-block rounded-full bg-emerald-100 px-2 py-0.5 text-[9px] font-black uppercase tracking-widest text-emerald-700">LTS</span>
                                                    @endif
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </template>
                                <template x-if="!osType">
                                    <p class="text-sm text-slate-400 italic">Pilih tipe OS terlebih dahulu.</p>
                                </template>
                            </div>
                            <x-input-error :messages="$errors->get('os_version')" class="mt-2" />
                        </div>

                        {{-- Purpose --}}
                        <div>
                            <x-input-label for="purpose" value="Keperluan / Tujuan Penggunaan" />
                            <textarea id="purpose" name="purpose" rows="4" class="panel-textarea" required
                                placeholder="Jelaskan untuk apa VM ini akan digunakan (praktikum, penelitian, proyek, dll.)">{{ old('purpose') }}</textarea>
                            <x-input-error :messages="$errors->get('purpose')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            {{-- Start Date --}}
                            <div>
                                <x-input-label for="start_date" value="Tanggal Mulai" />
                                <x-text-input id="start_date" class="panel-input" type="date"
                                    name="start_date"
                                    :value="old('start_date', now()->format('Y-m-d'))"
                                    min="{{ now()->format('Y-m-d') }}"
                                    required />
                                <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
                            </div>

                            {{-- Duration --}}
                            <div>
                                <x-input-label for="duration_days" value="Durasi Peminjaman (hari)" />
                                <x-text-input id="duration_days" class="panel-input" type="number"
                                    name="duration_days"
                                    :value="old('duration_days', 7)"
                                    min="1" max="365" required />
                                <p class="mt-1 text-xs text-slate-500">Admin dapat menyesuaikan durasi aktual saat ACC.</p>
                                <x-input-error :messages="$errors->get('duration_days')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-2">
                            <a href="{{ route('vm_borrow.index') }}" class="text-sm text-slate-500 hover:text-slate-800 font-bold">Batal</a>
                            <x-primary-button type="submit">
                                Kirim Permintaan VM
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function vmBorrowForm() {
                return {
                    osType: @js(old('os_type', 'debian')),
                    osVersion: @js(old('os_version', '')),
                }
            }
        </script>
    @endpush
</x-app-layout>