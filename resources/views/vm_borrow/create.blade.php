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

        <form action="{{ route('vm_borrow.store') }}" method="POST" x-data="vmBorrowForm()">
            @csrf

            {{-- Choose an OS --}}
            <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-neutral-100">
                    <h3 class="text-base font-semibold text-neutral-900">Pilih Sistem Operasi</h3>
                    <p class="text-sm text-neutral-500 mt-0.5">Pilih distribusi Linux yang ingin digunakan.</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Debian --}}
                        <label class="cursor-pointer group">
                            <input type="radio" name="os_type" value="debian" class="peer sr-only" {{ old('os_type', 'debian') === 'debian' ? 'checked' : '' }} @change="osType = 'debian'; osVersion = ''">
                            <div class="relative rounded-xl border-2 bg-white p-5 transition-all peer-checked:border-accent peer-checked:ring-2 peer-checked:ring-accent/20 peer-checked:shadow-md peer-checked:shadow-accent/5 border-neutral-200 group-hover:border-neutral-300 group-hover:shadow-sm">
                                {{-- Check indicator --}}
                                <div class="absolute top-3 right-3 w-5 h-5 rounded-full border-2 border-neutral-300 peer-checked:border-accent peer-checked:bg-accent flex items-center justify-center transition">
                                    <svg class="w-3 h-3 text-white hidden peer-checked:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                </div>

                                <div class="flex items-start gap-4">
                                    <svg class="w-12 h-12 flex-shrink-0" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                                        <rect x="1.5" y="1.5" width="45" height="45" rx="8" fill="#D70751"/>
                                        <g fill="#FFFFFF" transform="translate(24,23) scale(0.175) translate(-45,-55)">
                                            <path d="M64.525 62.053c-4.125.058.78 2.125 6.165 2.954 1.488-1.161 2.838-2.336 4.04-3.479-3.354.821-6.765.838-10.205.525m22.14-5.52c2.457-3.389 4.246-7.102 4.878-10.939-.551 2.736-2.035 5.099-3.435 7.592-7.711 4.854-.726-2.883-.004-5.824-8.29 10.436-1.138 6.257-1.439 9.171m8.174-21.265c.497-7.428-1.462-5.08-2.121-2.245.766.4 1.377 5.237 2.121 2.245M48.883-66.264c2.201.395 4.757.698 4.398 1.224 2.407-.528 2.954-1.015-4.398-1.224"/>
                                            <path d="m53.281-65.04-1.556.32 1.448-.127.108-.193"/>
                                            <path d="M121.93 38.085c.247 6.671-1.95 9.907-3.932 15.637l-3.564 1.781c-2.919 5.666.282 3.598-1.807 8.105-4.556 4.049-13.823 12.67-16.789 13.457-2.163-.047 1.469-2.554 1.943-3.537-6.097 4.188-4.894 6.285-14.217 8.83l-.273-.607C60.29 92.569 28.344 71.129 28.765 41.875c-.246 1.857-.698 1.393-1.208 2.144-1.186-15.052 6.952-30.17 20.675-36.343 13.427-6.646 29.163-3.918 38.78 5.044C81.73 5.8 71.217-1.534 58.757-.848c-12.208.193-23.625 7.95-27.436 16.369-6.253 3.938-6.979 15.177-9.704 17.233-3.665 26.943 6.896 38.583 24.762 52.275 2.812 1.896.792 2.184 1.173 3.627-5.936-2.779-11.372-6.976-15.841-12.114 2.372 3.473 4.931 6.847 8.239 9.499-5.596-1.897-13.074-13.563-15.256-14.038 9.647 17.274 39.142 30.295 54.587 23.836-7.146.263-16.226.146-24.256-2.822-3.371-1.734-7.958-5.331-7.14-6.003 21.079 7.875 42.854 5.965 61.09-8.655 4.641-3.614 9.709-9.761 11.173-9.846-2.206 3.317.377 1.596-1.318 4.523 4.625-7.456-2.008-3.035 4.779-12.877l2.507 3.453c-.931-6.188 7.687-13.704 6.813-23.492 1.975-2.994 2.206 3.22.107 10.107 2.912-7.64.767-8.867 1.516-15.171.81 2.118 1.867 4.37 2.412 6.606-1.895-7.382 1.948-12.433 2.898-16.724-.937-.415-2.928 3.264-3.383-5.457.065-3.788 1.054-1.985 1.435-2.917-.744-.427-2.694-3.33-3.88-8.9.86-1.308 2.3 3.393 3.47 3.586-.753-4.429-2.049-7.805-2.103-11.202-3.421-7.149-1.211.953-3.985-3.069-3.641-11.357 3.021-2.637 3.47-7.796 5.52 7.995 8.667 20.387 10.11 25.519-1.103-6.258-2.883-12.32-5.058-18.185 1.677.705-2.699-12.875 2.18-3.882-5.21-19.172-22.302-37.087-38.025-45.493 1.924 1.76 4.354 3.971 3.481 4.317-7.819-4.656-6.444-5.018-7.565-6.985-6.369-2.591-6.788.208-11.007.004-12.005-6.368-14.318-5.69-25.368-9.681l.502 2.349c-7.953-2.649-9.265 1.005-17.862.009-.523-.409 2.753-1.479 5.452-1.871-7.69 1.015-7.329-1.515-14.854.279 1.855-1.301 3.815-2.162 5.793-3.269-6.271.381-14.971 3.649-12.286.677C20.144-62.46 1.976-56.053-8.218-46.494l-.321-2.142c-4.672 5.608-20.371 16.748-21.622 24.011l-1.249.291c-2.431 4.116-4.004 8.781-5.932 13.016-3.18 5.417-4.661 2.085-4.208 2.934-6.253 12.679-9.359 23.332-12.043 32.069 1.912 2.858.046 17.206.769 28.688-3.141 56.709 39.8 111.77 86.737 124.48 6.88 2.459 17.11 2.364 25.813 2.618-10.268-2.937-11.595-1.556-21.595-5.044-7.215-3.398-8.797-7.277-13.907-11.711l2.022 3.573c-10.021-3.547-5.829-4.39-13.982-6.972l2.16-2.82c-3.249-.246-8.604-5.475-10.069-8.371l-3.553.14c-4.27-5.269-6.545-9.063-6.379-12.005l-1.148 2.047c-1.301-2.235-15.709-19.759-8.234-15.679-1.389-1.271-3.235-2.067-5.237-5.703l1.522-1.739c-3.597-4.627-6.621-10.562-6.391-12.536 1.919 2.592 3.25 3.075 4.568 3.52-9.083-22.539-9.593-1.242-16.474-22.942l1.456-.116c-1.116-1.682-1.793-3.506-2.69-5.298l.633-6.313c-6.541-7.562-1.829-32.151-.887-45.637.655-5.485 5.459-11.322 9.114-20.477l-2.227-.384C-27.316-2.419-7.271-24.81 2.011-23.658c4.499-5.649-.892-.02-1.772-1.443 9.878-10.223 12.984-7.222 19.65-9.061 7.19-4.268-6.17 1.664-2.761-1.628 12.427-3.174 8.808-7.216 25.021-8.828 1.71.973-3.969 1.503-5.395 2.766 10.354-5.066 32.769-3.914 47.326 2.811 16.895 7.896 35.873 31.232 36.622 53.189l.852.229c-.431 8.729 1.336 18.822-1.727 28.094l2.1-4.385"/>
                                            <path d="m19.5 67.715-.578 2.893c2.71 3.683 4.861 7.673 8.323 10.552-2.49-4.863-4.341-6.872-7.745-13.445m6.409-.251c-1.435-1.587-2.284-3.497-3.235-5.4.909 3.345 2.771 6.219 4.504 9.143l-1.269-3.743m113.411-24.65-.605 1.52c-1.111 7.892-3.511 15.701-7.189 22.941 4.06-7.639 6.69-15.995 7.79-24.461M49.698-68.243c2.789-1.022 6.855-.56 9.814-1.233-3.855.324-7.693.517-11.484 1.005l1.67.228m-97.917 52.067c.642 5.951-4.477 8.26 1.134 4.337 3.007-6.773-1.175-1.87-1.134-4.337m-6.593 27.538c1.292-3.967 1.526-6.349 2.02-8.645-3.571 4.566-1.643 5.539-2.02 8.645"/>
                                        </g>
                                    </svg>
                                    <div>
                                        <h4 class="font-semibold text-neutral-900">Debian</h4>
                                        <p class="text-xs text-neutral-500 mt-0.5">Stabil & andal untuk server</p>
                                        <div class="flex items-center gap-1.5 mt-2">
                                            @foreach($osVersions['debian'] as $ver)
                                                <span class="inline-flex items-center rounded-md bg-neutral-100 px-2 py-0.5 text-[11px] font-medium text-neutral-600">v{{ $ver }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </label>

                        {{-- Ubuntu --}}
                        <label class="cursor-pointer group">
                            <input type="radio" name="os_type" value="ubuntu" class="peer sr-only" {{ old('os_type') === 'ubuntu' ? 'checked' : '' }} @change="osType = 'ubuntu'; osVersion = ''">
                            <div class="relative rounded-xl border-2 bg-white p-5 transition-all peer-checked:border-accent peer-checked:ring-2 peer-checked:ring-accent/20 peer-checked:shadow-md peer-checked:shadow-accent/5 border-neutral-200 group-hover:border-neutral-300 group-hover:shadow-sm">
                                {{-- Check indicator --}}
                                <div class="absolute top-3 right-3 w-5 h-5 rounded-full border-2 border-neutral-300 peer-checked:border-accent peer-checked:bg-accent flex items-center justify-center transition">
                                    <svg class="w-3 h-3 text-white hidden peer-checked:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                </div>

                                <div class="flex items-start gap-4">
                                    <svg class="w-12 h-12 flex-shrink-0" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                                        <rect x="1.5" y="1.5" width="45" height="45" rx="8" fill="#DD4814"/>
                                        <g fill="#FFFFFF" transform="translate(24,24) scale(0.155)">
                                            <circle cx="-96.3772" r="18.9215"/>
                                            <path d="M-45.6059,68.395C-62.1655,57.3316-74.4844,40.4175-79.6011,20.6065-73.623,15.7354-69.8047,8.3164-69.8047,0-69.8047-8.3164-73.623-15.7354-79.6011-20.6065-74.4844-40.4175-62.1655-57.3316-45.6059-68.395L-31.7715-45.2212C-45.9824-35.2197-55.2754-18.7026-55.2754,0-55.2754,18.7026-45.9824,35.2197-31.7715,45.2212Z"/>
                                        </g>
                                        <g fill="#FFFFFF" transform="translate(24,24) scale(0.155) rotate(120)">
                                            <circle cx="-96.3772" r="18.9215"/>
                                            <path d="M-45.6059,68.395C-62.1655,57.3316-74.4844,40.4175-79.6011,20.6065-73.623,15.7354-69.8047,8.3164-69.8047,0-69.8047-8.3164-73.623-15.7354-79.6011-20.6065-74.4844-40.4175-62.1655-57.3316-45.6059-68.395L-31.7715-45.2212C-45.9824-35.2197-55.2754-18.7026-55.2754,0-55.2754,18.7026-45.9824,35.2197-31.7715,45.2212Z"/>
                                        </g>
                                        <g fill="#FFFFFF" transform="translate(24,24) scale(0.155) rotate(240)">
                                            <circle cx="-96.3772" r="18.9215"/>
                                            <path d="M-45.6059,68.395C-62.1655,57.3316-74.4844,40.4175-79.6011,20.6065-73.623,15.7354-69.8047,8.3164-69.8047,0-69.8047-8.3164-73.623-15.7354-79.6011-20.6065-74.4844-40.4175-62.1655-57.3316-45.6059-68.395L-31.7715-45.2212C-45.9824-35.2197-55.2754-18.7026-55.2754,0-55.2754,18.7026-45.9824,35.2197-31.7715,45.2212Z"/>
                                        </g>
                                    </svg>
                                    <div>
                                        <h4 class="font-semibold text-neutral-900">Ubuntu</h4>
                                        <p class="text-xs text-neutral-500 mt-0.5">Populer & serbaguna</p>
                                        <div class="flex items-center gap-1.5 mt-2">
                                            @foreach($osVersions['ubuntu'] as $ver)
                                                <span class="inline-flex items-center rounded-md bg-neutral-100 px-2 py-0.5 text-[11px] font-medium text-neutral-600 {{ in_array($ver, ['22.04', '24.04']) ? 'ring-1 ring-emerald-300 bg-emerald-50 text-emerald-700' : '' }}">{{ $ver }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>
                    <x-input-error :messages="$errors->get('os_type')" class="mt-3" />
                </div>
            </div>

            {{-- Choose Version --}}
            <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm overflow-hidden mt-6">
                <div class="px-6 py-5 border-b border-neutral-100">
                    <h3 class="text-base font-semibold text-neutral-900">Pilih Versi</h3>
                    <p class="text-sm text-neutral-500 mt-0.5">Tentukan versi sistem operasi.</p>
                </div>
                <div class="p-6">
                    <template x-if="osType === 'debian'">
                        <div>
                            <div class="flex flex-wrap gap-2">
                                @foreach($osVersions['debian'] as $ver)
                                    <label class="cursor-pointer">
                                        <input type="radio" name="os_version" value="{{ $ver }}" class="peer sr-only" {{ old('os_version') === $ver ? 'checked' : '' }} @change="osVersion = '{{ $ver }}'">
                                        <span class="inline-flex items-center px-5 py-2.5 rounded-lg border border-neutral-200 bg-white text-sm font-medium text-neutral-700 transition-all peer-checked:border-accent peer-checked:bg-accent-light peer-checked:text-accent peer-checked:ring-2 peer-checked:ring-accent/20 hover:border-neutral-300 cursor-pointer">
                                            Debian {{ $ver }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                            <p class="text-xs text-neutral-400 mt-3 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z"/></svg>
                                Debian cocok untuk server, deployment, dan proyek berbasis CLI.
                            </p>
                        </div>
                    </template>
                    <template x-if="osType === 'ubuntu'">
                        <div>
                            <div class="flex flex-wrap gap-2">
                                @foreach($osVersions['ubuntu'] as $ver)
                                    <label class="cursor-pointer">
                                        <input type="radio" name="os_version" value="{{ $ver }}" class="peer sr-only" {{ old('os_version') === $ver ? 'checked' : '' }} @change="osVersion = '{{ $ver }}'">
                                        <span class="relative inline-flex items-center px-5 py-2.5 rounded-lg border border-neutral-200 bg-white text-sm font-medium text-neutral-700 transition-all peer-checked:border-accent peer-checked:bg-accent-light peer-checked:text-accent peer-checked:ring-2 peer-checked:ring-accent/20 hover:border-neutral-300 cursor-pointer">
                                            Ubuntu {{ $ver }}
                                            @if(in_array($ver, ['22.04', '24.04']))
                                                <span class="absolute -top-1.5 -right-1.5 inline-flex items-center rounded-full bg-emerald-500 px-1.5 py-0.5 text-[9px] font-bold text-white shadow-sm">LTS</span>
                                            @endif
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                            <div class="flex items-start gap-2.5 bg-emerald-50 border border-emerald-200 rounded-xl px-4 py-3 mt-3">
                                <svg class="w-4 h-4 text-emerald-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87L18.18 22 12 18.56 5.82 22 7 14.14l-5-4.87 6.91-1.01L12 2z"/></svg>
                                <div>
                                    <p class="text-xs font-semibold text-emerald-800">Rekomendasi LTS</p>
                                    <p class="text-xs text-emerald-700 mt-0.5">Versi <strong>LTS</strong> (Long Term Support) direkomendasikan untuk tugas & praktikum — lebih stabil, dukungan keamanan 5 tahun.</p>
                                </div>
                            </div>
                        </div>
                    </template>
                    <template x-if="!osType">
                        <p class="text-sm text-neutral-400 italic">Pilih sistem operasi terlebih dahulu.</p>
                    </template>
                    <x-input-error :messages="$errors->get('os_version')" class="mt-3" />
                </div>
            </div>

            {{-- Purpose & Schedule --}}
            <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm overflow-hidden mt-6">
                <div class="px-6 py-5 border-b border-neutral-100">
                    <h3 class="text-base font-semibold text-neutral-900">Detail Peminjaman</h3>
                    <p class="text-sm text-neutral-500 mt-0.5">Jelaskan keperluan dan tentukan durasi peminjaman.</p>
                </div>
                <div class="p-6 space-y-5">
                    {{-- Purpose --}}
                    <div>
                        <x-input-label for="purpose" value="Keperluan / Tujuan Penggunaan" class="text-sm font-medium" />
                        <textarea id="purpose" name="purpose" rows="3" class="mt-1.5 block w-full rounded-xl border-neutral-200 bg-white shadow-sm focus:border-accent focus:ring-accent text-sm" required placeholder="Contoh: Praktikum Jaringan, Tugas Akhir, Penelitian...">{{ old('purpose') }}</textarea>
                        <x-input-error :messages="$errors->get('purpose')" class="mt-1" />
                    </div>

                    {{-- Date & Duration --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <x-input-label for="start_date" value="Tanggal Mulai" class="text-sm font-medium" />
                            <x-text-input id="start_date" type="date" name="start_date" :value="old('start_date', now()->format('Y-m-d'))" min="{{ now()->format('Y-m-d') }}" required class="mt-1.5 w-full" />
                            <x-input-error :messages="$errors->get('start_date')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="duration_days" value="Durasi (hari)" class="text-sm font-medium" />
                            <x-text-input id="duration_days" type="number" name="duration_days" :value="old('duration_days', 7)" min="1" max="365" required class="mt-1.5 w-full" />
                            <p class="text-xs text-neutral-400 mt-1">Admin dapat menyesuaikan durasi saat ACC.</p>
                            <x-input-error :messages="$errors->get('duration_days')" class="mt-1" />
                        </div>
                    </div>
                </div>
            </div>

            {{-- Submit --}}
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('vm_borrow.index') }}" class="text-sm font-medium text-neutral-500 hover:text-neutral-700 transition">Batal</a>
                <x-primary-button type="submit" size="sm">Kirim Permintaan VM</x-primary-button>
            </div>
        </form>
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