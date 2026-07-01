<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-start gap-4">
            <div>
                <h2 class="font-bold font-bold text-2xl text-slate-900 leading-tight">
                    {{ __('Detail Barang / Alat') }}
                </h2>
                <p class="text-sm text-slate-500 mt-2">Informasi lengkap barang yang tersimpan di inventaris.</p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('inventory_items.index') }}" class="inline-flex items-center px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-xs font-black uppercase tracking-widest hover:bg-slate-200 transition">Kembali</a>
                @if(Auth::user()->isAdmin() && $item->borrow_records_count === 0)
                    <form action="{{ route('inventory_items.destroy', $item) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2 rounded-xl bg-rose-600 text-white text-xs font-black uppercase tracking-widest hover:bg-rose-700 transition" onclick="return confirm('Hapus barang ini?')">Hapus</button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="panel-shell">
            @if(session('error'))
                <div class="panel-card mb-6">
                    <div class="panel-card-body">
                        <div class="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl" role="alert">
                            {{ session('error') }}
                        </div>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="panel-card p-8 lg:col-span-2">
                    <div class="flex flex-col gap-6">
                        <div>
                            <div class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2">Kode Barang</div>
                            <div class="font-bold text-3xl font-bold text-slate-900">{{ $item->code }}</div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <div class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2">Nama Barang</div>
                                <div class="text-lg font-bold text-slate-900">{{ $item->name }}</div>
                            </div>
                            <div>
                                <div class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2">Kategori</div>
                                <div class="text-lg font-bold text-slate-900">{{ $item->category ?? '-' }}</div>
                            </div>
                            <div>
                                <div class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2">Satuan</div>
                                <div class="text-lg font-bold text-slate-900">{{ $item->unit }}</div>
                            </div>
                            <div>
                                <div class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2">Lokasi</div>
                                <div class="text-lg font-bold text-slate-900">{{ $item->location ?? '-' }}</div>
                            </div>
                            <div>
                                <div class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2">Kondisi</div>
                                <div class="text-lg font-bold text-slate-900">{{ $item->condition }}</div>
                            </div>
                            <div>
                                <div class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2">Status</div>
                                <div class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-widest {{ $item->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                                </div>
                            </div>
                        </div>

                        @if($item->description)
                            <div>
                                <div class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2">Deskripsi</div>
                                <p class="text-slate-700 leading-relaxed">{{ $item->description }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="panel-card p-8">
                    <div class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Ringkasan Stok</div>
                    <div class="space-y-4">
                        <div>
                            <div class="text-sm text-slate-500 mb-1">Tersedia</div>
                            <div class="font-bold text-4xl font-bold text-cyan-700">{{ $item->available_stock }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-slate-500 mb-1">Total</div>
                            <div class="font-bold text-4xl font-bold text-slate-900">{{ $item->total_stock }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-slate-500 mb-1">Riwayat Peminjaman</div>
                            <div class="font-bold text-4xl font-bold text-emerald-600">{{ $item->borrow_records_count }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
