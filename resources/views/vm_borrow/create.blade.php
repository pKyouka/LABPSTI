<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-lg font-semibold text-neutral-800">{{ __('Ajukan Peminjaman Virtual Machine') }}</h2>
            <p class="text-sm text-neutral-500 mt-1">Isi formulir untuk mengajukan permintaan VM. Akan diproses oleh admin.</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if(session('success'))
            <div class="px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium rounded-xl">{{ session('success') }}</div>
        @endif

        <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-neutral-100">
                <h3 class="text-base font-semibold text-neutral-800">Detail Permintaan VM</h3>
                <p class="text-sm text-neutral-500 mt-0.5">Pilih sistem operasi, versi, dan isi informasi peminjaman.</p>
            </div>
            <div class="p-5">
                <form action="{{ route('vm_borrow.store') }}" method="POST" x-data="vmBorrowForm()" class="space-y-5">
                    @csrf

                    <div>
                        <x-input-label value="Sistem Operasi" />
                        <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <label class="cursor-pointer">
                                <input type="radio" name="os_type" value="debian" class="peer sr-only" {{ old('os_type', 'debian') === 'debian' ? 'checked' : '' }} @change="osType = 'debian'; osVersion = ''">
                                <div class="rounded-2xl border-2 bg-white p-5 transition peer-checked:border-accent peer-checked:ring-[3px] peer-checked:ring-accent/15 border-neutral-200 hover:border-neutral-300">
                                    <div class="inline-flex items-center rounded-full bg-neutral-800 px-3 py-1 text-[10px] font-semibold uppercase tracking-wider text-white">Debian</div>
                                    <h3 class="text-lg font-semibold text-neutral-800 mt-3">Debian GNU/Linux</h3>
                                    <p class="text-sm text-neutral-500 mt-1">Distro stabil. Tersedia versi 10, 11, 12.</p>
                                    <p class="text-xs font-medium text-accent mt-2" x-text="osType === 'debian' ? '✓ Dipilih' : 'Pilih →'"></p>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="os_type" value="ubuntu" class="peer sr-only" {{ old('os_type') === 'ubuntu' ? 'checked' : '' }} @change="osType = 'ubuntu'; osVersion = ''">
                                <div class="rounded-2xl border-2 bg-white p-5 transition peer-checked:border-accent peer-checked:ring-[3px] peer-checked:ring-accent/15 border-neutral-200 hover:border-neutral-300">
                                    <div class="inline-flex items-center rounded-full bg-neutral-800 px-3 py-1 text-[10px] font-semibold uppercase tracking-wider text-white">Ubuntu</div>
                                    <h3 class="text-lg font-semibold text-neutral-800 mt-3">Ubuntu Linux</h3>
                                    <p class="text-sm text-neutral-500 mt-1">Distro populer. Tersedia 22.04 hingga 24.04.</p>
                                    <p class="text-xs font-medium text-accent mt-2" x-text="osType === 'ubuntu' ? '✓ Dipilih' : 'Pilih →'"></p>
                                </div>
                            </label>
                        </div>
                        <x-input-error :messages="$errors->get('os_type')" />
                    </div>

                    <div>
                        <x-input-label for="os_version" value="Versi OS" />
                        <div class="mt-2">
                            <template x-if="osType === 'debian'">
                                <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                                    @foreach($osVersions['debian'] as $ver)
                                        <label class="cursor-pointer">
                                            <input type="radio" name="os_version" value="{{ $ver }}" class="peer sr-only" {{ old('os_version') === $ver ? 'checked' : '' }} @change="osVersion = '{{ $ver }}'">
                                            <div class="rounded-xl border border-neutral-200 bg-white px-3 py-3 text-center transition peer-checked:border-accent peer-checked:bg-accent-light peer-checked:text-accent text-sm font-medium">Debian {{ $ver }}</div>
                                        </label>
                                    @endforeach
                                </div>
                            </template>
                            <template x-if="osType === 'ubuntu'">
                                <div class="grid grid-cols-3 sm:grid-cols-5 gap-2">
                                    @foreach($osVersions['ubuntu'] as $ver)
                                        <label class="cursor-pointer">
                                            <input type="radio" name="os_version" value="{{ $ver }}" class="peer sr-only" {{ old('os_version') === $ver ? 'checked' : '' }} @change="osVersion = '{{ $ver }}'">
                                            <div class="rounded-xl border border-neutral-200 bg-white px-3 py-3 text-center transition peer-checked:border-accent peer-checked:bg-accent-light text-sm font-medium">Ubuntu {{ $ver }}</div>
                                        </label>
                                    @endforeach
                                </div>
                            </template>
                            <template x-if="!osType">
                                <p class="text-sm text-neutral-400 italic">Pilih tipe OS terlebih dahulu.</p>
                            </template>
                        </div>
                        <x-input-error :messages="$errors->get('os_version')" />
                    </div>

                    <div>
                        <x-input-label for="purpose" value="Keperluan / Tujuan Penggunaan" />
                        <textarea id="purpose" name="purpose" rows="4" class="fluent-textarea" required placeholder="Jelaskan untuk apa VM ini akan digunakan...">{{ old('purpose') }}</textarea>
                        <x-input-error :messages="$errors->get('purpose')" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="start_date" value="Tanggal Mulai" />
                            <x-text-input id="start_date" type="date" name="start_date" :value="old('start_date', now()->format('Y-m-d'))" min="{{ now()->format('Y-m-d') }}" required />
                            <x-input-error :messages="$errors->get('start_date')" />
                        </div>
                        <div>
                            <x-input-label for="duration_days" value="Durasi (hari)" />
                            <x-text-input id="duration_days" type="number" name="duration_days" :value="old('duration_days', 7)" min="1" max="365" required />
                            <p class="text-xs text-neutral-400 mt-1">Admin dapat menyesuaikan durasi saat ACC.</p>
                            <x-input-error :messages="$errors->get('duration_days')" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <a href="{{ route('vm_borrow.index') }}" class="fluent-btn fluent-btn-ghost fluent-btn-sm">Batal</a>
                        <x-primary-button type="submit" size="sm">Kirim Permintaan VM</x-primary-button>
                    </div>
                </form>
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