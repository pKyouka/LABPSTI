<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold text-neutral-800">{{ __('Edit Buletin') }}</h2>
    </x-slot>

    <div class="space-y-6">
        <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm overflow-hidden max-w-2xl">
            <div class="px-5 py-4 border-b border-neutral-100">
                <p class="text-sm text-neutral-500">Perbarui konten dan metadata buletin.</p>
            </div>
            <div class="p-5">
                <form action="{{ route('bulletins.update', $bulletin) }}" method="POST" enctype="multipart/form-data" x-data="{ titleLength: {{ strlen(old('title', $bulletin->title)) }} }" class="space-y-4">
                    @csrf @method('PUT')
                    <div>
                        <x-input-label for="title" :value="__('Judul')" />
                        <x-text-input id="title" type="text" name="title" :value="old('title', $bulletin->title)" required maxlength="255" @input="titleLength = $event.target.value.length" />
                        <p class="text-xs text-neutral-400 mt-1" x-text="titleLength + '/255 karakter'"></p>
                        <x-input-error :messages="$errors->get('title')" />
                    </div>
                    <div>
                        <x-input-label for="category_id" :value="__('Kategori Lab')" />
                        <select id="category_id" name="category_id" class="fluent-select">
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $bulletin->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('category_id')" />
                    </div>
                    <div>
                        <x-input-label for="content" :value="__('Konten')" />
                        <textarea id="content" name="content" rows="8" class="fluent-textarea" required>{{ old('content', $bulletin->content) }}</textarea>
                        <x-input-error :messages="$errors->get('content')" />
                    </div>
                    <div><x-input-label for="published_at" :value="__('Tanggal Publish')" /><x-text-input id="published_at" type="date" name="published_at" :value="old('published_at', $bulletin->published_at?->format('Y-m-d'))" /><x-input-error :messages="$errors->get('published_at')" /></div>
                    <div><x-input-label for="image" :value="__('Gambar')" /><input id="image" type="file" name="image" class="fluent-input" /><x-input-error :messages="$errors->get('image')" /></div>
                    <div class="flex items-center justify-end gap-2 pt-2">
                        <x-primary-button type="submit" size="sm">Perbarui</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>