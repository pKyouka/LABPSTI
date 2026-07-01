<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold font-bold text-2xl text-slate-900 leading-tight">
            {{ __('Input Penggunaan Lab') }}
        </h2>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="panel-shell">
            <div class="panel-card overflow-hidden">
                <div class="panel-card-head">
                    <p class="text-sm text-slate-500">Isi data penggunaan lab secara lengkap agar histori aktivitas tercatat dengan baik.</p>
                </div>
                <div class="panel-card-body">
                    <form action="{{ route('usage_records.store') }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="mb-4">
                                <x-input-label for="user_name" :value="__('Nama Pengguna')" />
                                <x-text-input id="user_name" class="panel-input" type="text" name="user_name" :value="old('user_name', $prefillName ?? '')" required autofocus :readonly="!($isAdmin ?? false)" />
                                <x-input-error :messages="$errors->get('user_name')" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <x-input-label for="user_id_number" :value="__('NIM / ID')" />
                                <x-text-input id="user_id_number" class="panel-input" type="text" name="user_id_number" :value="old('user_id_number', $prefillIdNumber ?? '')" required :readonly="!($isAdmin ?? false)" />
                                <x-input-error :messages="$errors->get('user_id_number')" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <x-input-label for="category_id" :value="__('Lab yang Digunakan')" />
                                <select id="category_id" name="category_id" class="panel-select">
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <x-input-label for="start_time" :value="__('Waktu Mulai')" />
                                <x-text-input id="start_time" class="panel-input" type="datetime-local" name="start_time" :value="old('start_time', now()->format('Y-m-d\TH:i'))" required />
                                <x-input-error :messages="$errors->get('start_time')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mb-4">
                            <x-input-label for="purpose" :value="__('Keperluan')" />
                            <textarea id="purpose" name="purpose" rows="4" class="panel-textarea" required>{{ old('purpose') }}</textarea>
                            <x-input-error :messages="$errors->get('purpose')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ml-4">
                                {{ __('Simpan Record') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
