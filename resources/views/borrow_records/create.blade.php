<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-lg font-semibold text-neutral-800">{{ __('Input Peminjaman Alat & Barang') }}</h2>
            <p class="text-sm text-neutral-500 mt-1">Klik kartu barang untuk memilih item. Daftar dikelompokkan per kategori.</p>
            <a href="{{ asset('documents/SURAT PENGAJUAN PEMINJAMAN PERALATAN LABORATORIUM RISET.docx') }}" download class="fluent-btn fluent-btn-primary fluent-btn-sm mt-3">Unduh Template Surat</a>
        </div>
    </x-slot>

    <div class="space-y-6" x-data="borrowCategoryAccordion()">
        @if(session('success'))
            <div class="px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium rounded-xl">{{ session('success') }}</div>
        @endif

        @if($isAdmin ?? false)
            <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm p-5">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4">
                    <div><h3 class="text-base font-semibold text-neutral-800">Tambah Barang Cepat</h3><p class="text-sm text-neutral-500">Admin bisa menambahkan barang langsung.</p></div>
                    <a href="{{ route('inventory_items.index') }}" class="fluent-btn fluent-btn-ghost fluent-btn-sm">Kelola Barang</a>
                </div>
                <form action="{{ route('inventory_items.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
                    @csrf
                    <input type="hidden" name="return_to_borrow" value="1">
                    <div><x-input-label for="qc" value="Kategori" /><x-text-input id="qc" type="text" name="category" :value="old('category')" placeholder="IoT" /></div>
                    <div><x-input-label for="qn" value="Nama" /><x-text-input id="qn" type="text" name="name" :value="old('name')" required /></div>
                    <div><x-input-label for="qs" value="Total Stok" /><x-text-input id="qs" type="number" min="1" name="total_stock" :value="old('total_stock', 1)" required /></div>
                    <div><x-primary-button type="submit" size="sm">Tambah</x-primary-button></div>
                </form>
            </div>
        @endif

        <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm p-5">
            <p class="text-sm text-neutral-500 mb-4">Pilih kategori untuk melihat daftar item.</p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($categoryCards as $categoryCard)
                    @php $categoryItems = $groupedItems[$categoryCard['key']] ?? collect(); @endphp
                    <button type="button" class="rounded-2xl border-2 bg-white p-5 text-left transition" :class="isOpen(@js($categoryCard['key'])) ? 'border-accent ring-[3px] ring-accent/15' : 'border-neutral-200 hover:border-neutral-300'" @click="toggleCategory(@js($categoryCard['key']))">
                        <div class="inline-flex items-center rounded-full bg-neutral-800 px-3 py-1 text-[10px] font-semibold uppercase tracking-wider text-white">{{ $categoryCard['label'] }}</div>
                        <h3 class="text-lg font-semibold text-neutral-800 mt-3">{{ $categoryCard['key'] }}</h3>
                        <p class="text-sm text-neutral-500 mt-1">{{ $categoryCard['description'] }}</p>
                        <p class="text-xs font-medium text-accent mt-2 flex justify-between"><span>{{ $categoryItems->count() }} item</span><span x-text="isOpen(@js($categoryCard['key'])) ? 'Tutup' : 'Lihat'">Lihat</span></p>
                    </button>
                @endforeach
            </div>

            <div class="mt-6 space-y-6">
                @foreach($categoryCards as $categoryCard)
                    @php $categoryItems = $groupedItems[$categoryCard['key']] ?? collect(); @endphp
                    <section x-show="isOpen(@js($categoryCard['key']))" x-transition>
                        <div class="flex items-center justify-between mb-3">
                            <div><h3 class="text-lg font-semibold text-neutral-800">{{ $categoryCard['key'] }}</h3><p class="text-sm text-neutral-500">Klik item untuk memilih.</p></div>
                            <span class="text-xs font-medium text-neutral-400">{{ $categoryItems->count() }} item</span>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3" x-data="borrowCardPicker()" x-init="init(@js(old('inventory_item_id')))">
                            @forelse($categoryItems as $item)
                                <button type="button" class="rounded-2xl border-2 bg-white p-4 text-left transition" :class="selectedItemId == {{ $item->id }} ? 'border-accent ring-[3px] ring-accent/15' : 'border-neutral-200 hover:border-neutral-300'" @click="select({{ $item->id }})">
                                    <div class="inline-flex items-center rounded-full bg-neutral-800 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wider text-white">{{ $categoryCard['label'] }}</div>
                                    <p class="text-sm font-semibold text-neutral-800 mt-2 line-clamp-2">{{ $item->name }}</p>
                                    <p class="text-xs text-neutral-500 mt-1">Sisa {{ $item->available_stock }}/{{ $item->total_stock }} {{ $item->unit }}</p>
                                    <p class="text-xs font-medium text-accent mt-1">Pilih →</p>
                                </button>
                            @empty
                                <div class="col-span-full rounded-2xl border border-dashed border-neutral-200 bg-neutral-50 p-8 text-center text-sm text-neutral-500">Belum ada barang.</div>
                            @endforelse
                        </div>
                    </section>
                @endforeach
            </div>

            <div class="mt-8 bg-white rounded-2xl border border-neutral-200 p-5" x-data="borrowCardPicker()" x-init="init(@js(old('inventory_item_id')))">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4">
                    <h3 class="text-base font-semibold text-neutral-800">Form Peminjaman</h3>
                    <p class="text-sm text-neutral-500">Dipilih: <span class="font-medium text-neutral-800" x-text="selectedItemName || 'Belum ada'"></span></p>
                </div>
                <form id="borrow-form" action="{{ route('borrow_records.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <input type="hidden" name="inventory_item_id" :value="selectedItemId">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div><x-input-label for="user_name" :value="__('Nama Peminjam')" /><x-text-input id="user_name" type="text" name="user_name" :value="old('user_name', $prefillName ?? '')" required :readonly="!($isAdmin ?? false)" /><x-input-error :messages="$errors->get('user_name')" /></div>
                        <div><x-input-label for="user_id_number" :value="__('NIM / ID')" /><x-text-input id="user_id_number" type="text" name="user_id_number" :value="old('user_id_number', $prefillIdNumber ?? '')" required :readonly="!($isAdmin ?? false)" /><x-input-error :messages="$errors->get('user_id_number')" /></div>
                        <div><x-input-label for="quantity" :value="__('Jumlah')" /><x-text-input id="quantity" type="number" min="1" name="quantity" :value="old('quantity', 1)" required /><x-input-error :messages="$errors->get('quantity')" /></div>
                        <div><x-input-label for="borrowed_at" :value="__('Waktu Pinjam')" /><x-text-input id="borrowed_at" type="datetime-local" name="borrowed_at" :value="old('borrowed_at', now()->format('Y-m-d\TH:i'))" required /><x-input-error :messages="$errors->get('borrowed_at')" /></div>
                        <div><x-input-label for="due_at" :value="__('Batas Pengembalian')" /><x-text-input id="due_at" type="datetime-local" name="due_at" :value="old('due_at')" /><x-input-error :messages="$errors->get('due_at')" /></div>
                    </div>
                    <div><x-input-label for="purpose" :value="__('Keperluan')" /><textarea id="purpose" name="purpose" rows="3" class="fluent-textarea" required>{{ old('purpose') }}</textarea><x-input-error :messages="$errors->get('purpose')" /></div>
                    <div><x-input-label for="notes" :value="__('Catatan')" /><textarea id="notes" name="notes" rows="2" class="fluent-textarea">{{ old('notes') }}</textarea><x-input-error :messages="$errors->get('notes')" /></div>
                    <div><x-input-label for="request_pdf" :value="__('PDF Peminjaman')" /><input id="request_pdf" type="file" name="request_pdf" accept="application/pdf" class="fluent-input" required /><p class="text-xs text-neutral-400 mt-1">Unggah surat/formulir peminjaman (PDF).</p><x-input-error :messages="$errors->get('request_pdf')" /></div>
                    <div class="flex justify-end pt-2"><x-primary-button type="submit" size="sm">Simpan Peminjaman</x-primary-button></div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function borrowCategoryAccordion(){return{openCategory:null,toggleCategory(k){this.openCategory=this.openCategory===k?null:k},isOpen(k){return this.openCategory===k}}}
        function borrowCardPicker(){return{selectedItemId:'',selectedItemName:'',catalog:@js($items->keyBy('id')->map(fn($i)=>['name'=>$i->name])),init(id){if(id)this.select(id,false)},select(id,focus=true){this.selectedItemId=String(id);const i=this.catalog[id];this.selectedItemName=i?i.name:'';if(focus)document.getElementById('borrow-form')?.scrollIntoView({behavior:'smooth',block:'start'})}}}
    </script>
    @endpush
</x-app-layout>