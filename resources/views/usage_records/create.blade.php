<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold text-neutral-800">{{ __('Input Penggunaan Lab') }}</h2>
    </x-slot>

    <div class="space-y-6">
        <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-neutral-100">
                <p class="text-sm text-neutral-500">Isi data penggunaan lab secara lengkap.</p>
            </div>
            <div class="p-5">
                <form action="{{ route('usage_records.store') }}" method="POST" class="space-y-4 max-w-2xl">
                    @csrf
                    <div>
                        <x-input-label for="user_name" :value="__('Nama Pengguna')" />
                        <x-text-input id="user_name" type="text" name="user_name" :value="old('user_name', $prefillName ?? '')" required autofocus :readonly="!($isAdmin ?? false)" />
                        <x-input-error :messages="$errors->get('user_name')" />
                    </div>
                    <div>
                        <x-input-label for="user_id_number" :value="__('NIM / ID')" />
                        <x-text-input id="user_id_number" type="text" name="user_id_number" :value="old('user_id_number', $prefillIdNumber ?? '')" required :readonly="!($isAdmin ?? false)" />
                        <x-input-error :messages="$errors->get('user_id_number')" />
                    </div>
                    <div>
                        <x-input-label for="category_id" :value="__('Lab yang Digunakan')" />
                        <select id="category_id" name="category_id" class="fluent-select">
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('category_id')" />
                    </div>
                    <div>
                        <x-input-label for="start_time" :value="__('Waktu Mulai')" />
                        <x-text-input id="start_time" type="datetime-local" name="start_time" :value="old('start_time', now()->format('Y-m-d\TH:i'))" required />
                        <x-input-error :messages="$errors->get('start_time')" />
                    </div>
                    <div>
                        <x-input-label for="purpose" :value="__('Keperluan')" />
                        <textarea id="purpose" name="purpose" rows="3" class="fluent-textarea" required>{{ old('purpose') }}</textarea>
                        <x-input-error :messages="$errors->get('purpose')" />
                    </div>
                    <div class="flex items-center justify-end gap-2">
                        <x-primary-button type="submit" size="sm">Simpan</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>