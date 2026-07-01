<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold font-bold text-2xl text-slate-900 leading-tight">
            {{ __('Tambah Buletin Baru') }}
        </h2>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="panel-shell">
            <div class="panel-card overflow-hidden">
                <div class="panel-card-head">
                    <p class="text-sm text-slate-500">Lengkapi informasi buletin untuk dipublikasikan pada halaman kategori lab.</p>
                </div>
                <div class="panel-card-body">
                    <form action="{{ route('bulletins.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4" x-data="{ titleLength: {{ strlen(old('title', '')) }} }">
                            <div class="flex justify-between items-center">
                                <x-input-label for="title" :value="__('Judul')" />
                                <span class="text-xs text-slate-500" x-text="titleLength + '/255'"></span>
                            </div>
                            <x-text-input id="title" class="panel-input" type="text" name="title" :value="old('title')" required autofocus maxlength="255"
                                @input="titleLength = $event.target.value.length" />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="category_id" :value="__('Kategori Lab')" />
                            <select id="category_id" name="category_id" class="panel-select">
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="published_at" :value="__('Tanggal Publish')" />
                            <x-text-input id="published_at" class="panel-input" type="date" name="published_at" :value="old('published_at')" />
                            <x-input-error :messages="$errors->get('published_at')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="image" :value="__('Gambar')" />
                            <input id="image" type="file" name="image" class="panel-input" />
                            <x-input-error :messages="$errors->get('image')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="content" :value="__('Konten')" />
                            <textarea id="editor" name="content" class="panel-textarea">{{ old('content') }}</textarea>
                            <x-input-error :messages="$errors->get('content')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ml-4" x-data="{ loading: false }" @click="loading = true" :disabled="loading">
                                <span x-show="!loading">{{ __('Simpan Buletin') }}</span>
                                <span x-show="loading" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                    Menyimpan...
                                </span>
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>
    <script>
        ClassicEditor
            .create(document.querySelector('#editor'))
            .catch(error => {
                console.error(error);
            });
    </script>
    @endpush
</x-app-layout>
