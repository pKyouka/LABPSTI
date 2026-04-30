<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display font-bold text-2xl text-slate-900 leading-tight">
            {{ __('Tambah Barang / Alat') }}
        </h2>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="panel-shell">
            <div class="panel-card overflow-hidden">
                <div class="panel-card-head">
                    <p class="text-sm text-slate-500">Masukkan data inventaris agar barang bisa dipilih saat proses peminjaman.</p>
                </div>
                <div class="panel-card-body">
                    <form action="{{ route('inventory_items.store') }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="mb-4">
                                <x-input-label for="code" :value="__('Kode Barang')" />
                                <x-text-input id="code" class="panel-input" type="text" name="code" :value="old('code')" required autofocus />
                                <x-input-error :messages="$errors->get('code')" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <x-input-label for="name" :value="__('Nama Barang')" />
                                <x-text-input id="name" class="panel-input" type="text" name="name" :value="old('name')" required />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <x-input-label for="category" :value="__('Kategori')" />
                                <x-text-input id="category" class="panel-input" type="text" name="category" :value="old('category')" />
                                <x-input-error :messages="$errors->get('category')" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <x-input-label for="unit" :value="__('Satuan')" />
                                <x-text-input id="unit" class="panel-input" type="text" name="unit" :value="old('unit', 'unit')" required />
                                <x-input-error :messages="$errors->get('unit')" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <x-input-label for="total_stock" :value="__('Total Stok')" />
                                <x-text-input id="total_stock" class="panel-input" type="number" min="1" name="total_stock" :value="old('total_stock', 1)" required />
                                <x-input-error :messages="$errors->get('total_stock')" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <x-input-label for="available_stock" :value="__('Stok Tersedia')" />
                                <x-text-input id="available_stock" class="panel-input" type="number" min="0" name="available_stock" :value="old('available_stock')" />
                                <x-input-error :messages="$errors->get('available_stock')" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <x-input-label for="location" :value="__('Lokasi')" />
                                <x-text-input id="location" class="panel-input" type="text" name="location" :value="old('location')" />
                                <x-input-error :messages="$errors->get('location')" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <x-input-label for="condition" :value="__('Kondisi')" />
                                <x-text-input id="condition" class="panel-input" type="text" name="condition" :value="old('condition', 'baik')" required />
                                <x-input-error :messages="$errors->get('condition')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mb-4">
                            <x-input-label for="description" :value="__('Deskripsi')" />
                            <textarea id="description" name="description" rows="4" class="panel-textarea">{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <label class="inline-flex items-center gap-3 mb-4">
                            <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300 text-cyan-600 shadow-sm focus:ring-cyan-500" @checked(old('is_active', true))>
                            <span class="text-sm font-semibold text-slate-700">Barang aktif dan bisa dipinjam</span>
                        </label>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ml-4">
                                {{ __('Simpan Barang') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
