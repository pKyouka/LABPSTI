<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3">
            <h2 class="font-bold font-bold text-2xl text-slate-900 leading-tight">
                {{ __('Input Peminjaman Alat & Barang') }}
            </h2>
            <p class="text-sm text-slate-500 max-w-3xl">
                Klik kartu barang untuk memilih item yang akan dipinjam. Daftar di bawah sudah dikelompokkan per kategori agar cepat dipindai.
            </p>
            <div class="mt-3">
                <a href="{{ asset('documents/SURAT PENGAJUAN PEMINJAMAN PERALATAN LABORATORIUM RISET.docx') }}" download class="inline-flex items-center px-5 py-2.5 rounded-xl bg-emerald-600 text-white text-xs font-black uppercase tracking-widest hover:bg-emerald-700 transition shadow-lg shadow-emerald-200">
                    Unduh Template Surat Peminjaman
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="panel-shell space-y-6" x-data="borrowCategoryAccordion()">
            @if(session('success'))
                <div class="panel-card">
                    <div class="panel-card-body">
                        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl" role="alert">
                            {{ session('success') }}
                        </div>
                    </div>
                </div>
            @endif

            @if($isAdmin ?? false)
                <div class="panel-card overflow-hidden">
                    <div class="panel-card-head flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="text-lg font-black text-slate-900">Tambah barang cepat</h3>
                            <p class="text-sm text-slate-500">Admin bisa menambahkan barang baru langsung dari halaman peminjaman.</p>
                        </div>
                        <a href="{{ route('inventory_items.index') }}" class="inline-flex items-center px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-black uppercase tracking-widest hover:bg-slate-800 transition">Kelola Barang</a>
                    </div>
                    <div class="panel-card-body">
                        <form action="{{ route('inventory_items.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 items-end">
                            @csrf
                            <input type="hidden" name="return_to_borrow" value="1">

                            <div>
                                <x-input-label for="quick_category" :value="__('Kategori')" />
                                <x-text-input id="quick_category" class="panel-input" type="text" name="category" :value="old('category')" placeholder="Misal: Barang IoT" />
                            </div>

                            <div>
                                <x-input-label for="quick_name" :value="__('Nama Barang')" />
                                <x-text-input id="quick_name" class="panel-input" type="text" name="name" :value="old('name')" required />
                            </div>

                            <div>
                                <x-input-label for="quick_total_stock" :value="__('Total Stok')" />
                                <x-text-input id="quick_total_stock" class="panel-input" type="number" min="1" name="total_stock" :value="old('total_stock', 1)" required />
                            </div>

                            <div>
                                <x-primary-button type="submit" class="w-full justify-center">Tambah Barang</x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            <div class="panel-card overflow-hidden">
                <div class="panel-card-head">
                    <p class="text-sm text-slate-500">Pilih kategori dulu, lalu klik lagi untuk menutup daftar item di dalamnya.</p>
                </div>
                <div class="panel-card-body" x-data="borrowCardPicker()" x-init="init(@js(old('inventory_item_id')))">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach($categoryCards as $categoryCard)
                            @php
                                $categoryItems = $groupedItems[$categoryCard['key']] ?? collect();
                            @endphp
                            <button
                                type="button"
                                class="relative overflow-hidden rounded-3xl border-2 bg-white p-5 text-left shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl"
                                :class="isOpen(@js($categoryCard['key'])) ? 'border-cyan-500 ring-4 ring-cyan-100 shadow-lg shadow-cyan-100' : 'border-slate-200 hover:border-cyan-300'"
                                @click="toggleCategory(@js($categoryCard['key']))"
                            >
                                <div class="absolute inset-0 bg-gradient-to-br from-cyan-50 via-white to-blue-50 opacity-0 transition-opacity duration-300" :class="isOpen(@js($categoryCard['key'])) ? 'opacity-100' : 'group-hover:opacity-100'"></div>
                                <div class="relative z-10 flex h-full min-h-44 flex-col justify-between gap-4">
                                    <div class="space-y-3">
                                        <div class="inline-flex items-center rounded-full bg-slate-900 px-3 py-1.5 text-[10px] font-black uppercase tracking-widest text-white">
                                            {{ $categoryCard['label'] }}
                                        </div>
                                        <div>
                                            <h3 class="text-xl font-black text-slate-900">{{ $categoryCard['key'] }}</h3>
                                            <p class="mt-1 text-sm text-slate-500">{{ $categoryCard['description'] }}</p>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between text-xs font-black uppercase tracking-widest text-cyan-700">
                                        <span>{{ $categoryItems->count() }} item</span>
                                        <span x-text="isOpen(@js($categoryCard['key'])) ? 'Tutup' : 'Lihat'">Lihat</span>
                                    </div>
                                </div>
                            </button>
                        @endforeach
                    </div>

                    <div class="mt-8 space-y-8">
                        @foreach($categoryCards as $categoryCard)
                            @php
                                $categoryItems = $groupedItems[$categoryCard['key']] ?? collect();
                            @endphp
                            <section x-show="isOpen(@js($categoryCard['key']))" x-transition>
                                <div class="flex items-center justify-between gap-4 mb-4">
                                    <div>
                                        <h3 class="text-xl font-black text-slate-900">{{ $categoryCard['key'] }}</h3>
                                        <p class="text-sm text-slate-500">Klik salah satu item di bawah untuk memilih barangnya.</p>
                                    </div>
                                    <span class="text-[11px] font-black uppercase tracking-[0.22em] text-slate-400">{{ $categoryItems->count() }} item</span>
                                </div>

                                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                                    @forelse($categoryItems as $item)
                                        <button
                                            type="button"
                                            class="group relative aspect-square rounded-3xl border-2 border-slate-200 bg-white p-4 text-left shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl hover:border-cyan-300 overflow-hidden"
                                            :class="selectedItemId == {{ $item->id }} ? 'border-cyan-500 ring-4 ring-cyan-100 shadow-lg shadow-cyan-100' : ''"
                                            @click="select({{ $item->id }})"
                                        >
                                            <div class="absolute inset-0 bg-gradient-to-br from-cyan-50 via-white to-blue-50 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                            <div class="relative z-10 flex h-full flex-col justify-between gap-3">
                                                <div class="space-y-2">
                                                    <div class="inline-flex items-center rounded-full bg-slate-900 px-2.5 py-1 text-[10px] font-black uppercase tracking-widest text-white">
                                                        {{ $categoryCard['label'] }}
                                                    </div>
                                                    <div class="text-base font-black leading-snug text-slate-900 line-clamp-3">{{ $item->name }}</div>
                                                </div>
                                                <div class="space-y-1">
                                                    <div class="text-xs font-bold text-slate-500">Sisa {{ $item->available_stock }} / {{ $item->total_stock }} {{ $item->unit }}</div>
                                                    <div class="inline-flex items-center gap-2 text-xs font-black uppercase tracking-widest text-cyan-700">
                                                        Pilih barang
                                                        <span class="transition-transform group-hover:translate-x-1">→</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </button>
                                    @empty
                                        <div class="col-span-full rounded-3xl border border-dashed border-slate-200 bg-slate-50 p-10 text-center text-slate-500">
                                            Belum ada barang pada kategori ini.
                                        </div>
                                    @endforelse
                                </div>
                            </section>
                        @endforeach
                    </div>

                    <div class="mt-8 rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
                            <div>
                                <h3 class="text-lg font-black text-slate-900">Form Peminjaman</h3>
                                <p class="text-sm text-slate-500">Data peminjam diisi setelah item dipilih dari kartu.</p>
                            </div>
                            <div class="rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-600">
                                <span class="font-bold text-slate-900">Dipilih:</span>
                                <span x-text="selectedItemName || 'Belum ada item dipilih'"></span>
                            </div>
                        </div>

                        <form id="borrow-form" action="{{ route('borrow_records.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="inventory_item_id" :value="selectedItemId">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="mb-4">
                                    <x-input-label for="user_name" :value="__('Nama Peminjam')" />
                                    <x-text-input id="user_name" class="panel-input" type="text" name="user_name" :value="old('user_name', $prefillName ?? '')" required autofocus :readonly="!($isAdmin ?? false)" />
                                    <x-input-error :messages="$errors->get('user_name')" class="mt-2" />
                                </div>

                                <div class="mb-4">
                                    <x-input-label for="user_id_number" :value="__('NIM / ID')" />
                                    <x-text-input id="user_id_number" class="panel-input" type="text" name="user_id_number" :value="old('user_id_number', $prefillIdNumber ?? '')" required :readonly="!($isAdmin ?? false)" />
                                    <x-input-error :messages="$errors->get('user_id_number')" class="mt-2" />
                                </div>

                                <div class="mb-4">
                                    <x-input-label for="quantity" :value="__('Jumlah')" />
                                    <x-text-input id="quantity" class="panel-input" type="number" min="1" name="quantity" :value="old('quantity', 1)" required />
                                    <x-input-error :messages="$errors->get('quantity')" class="mt-2" />
                                </div>

                                <div class="mb-4">
                                    <x-input-label for="borrowed_at" :value="__('Waktu Pinjam')" />
                                    <x-text-input id="borrowed_at" class="panel-input" type="datetime-local" name="borrowed_at" :value="old('borrowed_at', now()->format('Y-m-d\TH:i'))" required />
                                    <x-input-error :messages="$errors->get('borrowed_at')" class="mt-2" />
                                </div>

                                <div class="mb-4">
                                    <x-input-label for="due_at" :value="__('Batas Pengembalian')" />
                                    <x-text-input id="due_at" class="panel-input" type="datetime-local" name="due_at" :value="old('due_at')" />
                                    <x-input-error :messages="$errors->get('due_at')" class="mt-2" />
                                </div>
                            </div>

                            <div class="mb-4">
                                <x-input-label for="purpose" :value="__('Keperluan')" />
                                <textarea id="purpose" name="purpose" rows="4" class="panel-textarea" required>{{ old('purpose') }}</textarea>
                                <x-input-error :messages="$errors->get('purpose')" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <x-input-label for="notes" :value="__('Catatan')" />
                                <textarea id="notes" name="notes" rows="3" class="panel-textarea">{{ old('notes') }}</textarea>
                                <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <x-input-label for="request_pdf" :value="__('PDF Peminjaman')" />
                                <input id="request_pdf" type="file" name="request_pdf" accept="application/pdf" class="panel-input" required />
                                <p class="mt-2 text-xs text-slate-500">Unggah surat atau formulir peminjaman dalam format PDF.</p>
                                <x-input-error :messages="$errors->get('request_pdf')" class="mt-2" />
                            </div>

                            <div class="flex items-center justify-end mt-4">
                                <x-primary-button class="ml-4" :disabled="count($items) === 0">
                                    {{ __('Simpan Peminjaman') }}
                                </x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function borrowCategoryAccordion() {
                return {
                    openCategory: null,
                    toggleCategory(categoryKey) {
                        this.openCategory = this.openCategory === categoryKey ? null : categoryKey;
                    },
                    isOpen(categoryKey) {
                        return this.openCategory === categoryKey;
                    },
                }
            }

            function borrowCardPicker() {
                return {
                    selectedItemId: '',
                    selectedItemName: '',
                    catalog: @js($items->keyBy('id')->map(function ($item) {
                        return [
                            'name' => $item->name,
                            'category' => $item->category,
                            'stock' => $item->available_stock,
                            'unit' => $item->unit,
                        ];
                    })),
                    init(initialId) {
                        if (initialId) {
                            this.select(initialId, false);
                        }
                    },
                    select(id, focus = true) {
                        this.selectedItemId = String(id);
                        const item = this.catalog[id];
                        this.selectedItemName = item ? item.name : '';

                        if (focus) {
                            const target = document.getElementById('borrow-form');
                            if (target) {
                                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                            }
                        }
                    },
                }
            }
        </script>
    @endpush
</x-app-layout>
