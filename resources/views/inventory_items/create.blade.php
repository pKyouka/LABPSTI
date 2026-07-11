<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold text-neutral-800">{{ __('Tambah Barang / Alat') }}</h2>
    </x-slot>

    <div class="space-y-6">
        <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm overflow-hidden max-w-2xl">
            <div class="px-5 py-4 border-b border-neutral-100">
                <p class="text-sm text-neutral-500">Masukkan data inventaris.</p>
            </div>
            <div class="p-5">
                <form action="{{ route('inventory_items.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div><x-input-label for="code" :value="__('Kode Barang')" /><x-text-input id="code" type="text" name="code" :value="old('code')" required /><x-input-error :messages="$errors->get('code')" /></div>
                        <div><x-input-label for="name" :value="__('Nama Barang')" /><x-text-input id="name" type="text" name="name" :value="old('name')" required /><x-input-error :messages="$errors->get('name')" /></div>
                        <div><x-input-label for="category" :value="__('Kategori')" /><x-text-input id="category" type="text" name="category" :value="old('category')" /><x-input-error :messages="$errors->get('category')" /></div>
                        <div><x-input-label for="unit" :value="__('Satuan')" /><x-text-input id="unit" type="text" name="unit" :value="old('unit', 'unit')" required /><x-input-error :messages="$errors->get('unit')" /></div>
                        <div><x-input-label for="total_stock" :value="__('Total Stok')" /><x-text-input id="total_stock" type="number" min="1" name="total_stock" :value="old('total_stock', 1)" required /><x-input-error :messages="$errors->get('total_stock')" /></div>
                        <div><x-input-label for="available_stock" :value="__('Stok Tersedia')" /><x-text-input id="available_stock" type="number" min="0" name="available_stock" :value="old('available_stock')" /><x-input-error :messages="$errors->get('available_stock')" /></div>
                        <div><x-input-label for="location" :value="__('Lokasi')" /><x-text-input id="location" type="text" name="location" :value="old('location')" /><x-input-error :messages="$errors->get('location')" /></div>
                        <div><x-input-label for="condition" :value="__('Kondisi')" /><x-text-input id="condition" type="text" name="condition" :value="old('condition', 'baik')" required /><x-input-error :messages="$errors->get('condition')" /></div>
                    </div>
                    <div class="flex items-center justify-end gap-2 pt-2">
                        <x-primary-button type="submit" size="sm">Simpan</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>