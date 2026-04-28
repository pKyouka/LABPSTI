<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display font-bold text-2xl text-slate-900 leading-tight">
            {{ __('Edit Buletin') }}
        </h2>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="panel-shell">
            <div class="panel-card overflow-hidden">
                <div class="panel-card-head">
                    <p class="text-sm text-slate-500">Perbarui konten dan metadata buletin sebelum dipublikasikan ulang.</p>
                </div>
                <div class="panel-card-body">
                    <form action="{{ route('bulletins.update', $bulletin) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <x-input-label for="title" :value="__('Judul')" />
                            <x-text-input id="title" class="panel-input" type="text" name="title" :value="old('title', $bulletin->title)" required autofocus />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="category_id" :value="__('Kategori Lab')" />
                            <select id="category_id" name="category_id" class="panel-select">
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $bulletin->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="published_at" :value="__('Tanggal Publish')" />
                            <x-text-input id="published_at" class="panel-input" type="date" name="published_at" :value="old('published_at', $bulletin->published_at?->format('Y-m-d'))" />
                            <x-input-error :messages="$errors->get('published_at')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="image" :value="__('Gambar')" />
                            @if($bulletin->image)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $bulletin->image) }}" alt="{{ $bulletin->title }}" class="h-32 object-cover rounded-xl border border-slate-200">
                                </div>
                            @endif
                            <input id="image" type="file" name="image" class="panel-input" />
                            <x-input-error :messages="$errors->get('image')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="content" :value="__('Konten')" />
                            <textarea id="editor" name="content" class="panel-textarea">{{ old('content', $bulletin->content) }}</textarea>
                            <x-input-error :messages="$errors->get('content')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ml-4">
                                {{ __('Update Buletin') }}
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
