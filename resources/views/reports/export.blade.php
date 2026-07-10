<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-4">
            <h2 class="font-bold text-2xl text-slate-900 leading-tight">
                {{ __('Export Excel Laporan') }}
            </h2>
            <a href="{{ route('reports.index') }}" class="inline-flex items-center px-6 py-2.5 bg-slate-600 text-white font-black rounded-xl hover:bg-slate-700 transition shadow-lg shadow-slate-200 uppercase tracking-widest text-[10px]">
                Kembali ke Laporan
            </a>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="panel-shell">
            <!-- Peminjaman Alat Export -->
            <div class="panel-card p-8 mb-6">
                <h3 class="text-lg font-black text-slate-900 mb-6 flex items-center">
                    <svg class="w-5 h-5 mr-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                    Export Laporan Peminjaman Alat & Barang (Excel)
                </h3>
                <form method="GET" action="{{ route('reports.borrow.export') }}" class="flex flex-wrap items-end gap-4" target="_blank">
                    <div>
                        <label class="block text-xs font-black text-slate-700 uppercase tracking-widest mb-2">Tanggal Mulai</label>
                        <input type="date" name="start_date" value="{{ old('start_date', $defaultStart) }}"
                               class="rounded-lg border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-700 uppercase tracking-widest mb-2">Tanggal Akhir</label>
                        <input type="date" name="end_date" value="{{ old('end_date', $defaultEnd) }}"
                               class="rounded-lg border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                    <div>
                        <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-emerald-600 text-white font-black rounded-lg hover:bg-emerald-700 transition text-xs uppercase tracking-widest">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            Export Excel
                        </button>
                    </div>
                </form>
            </div>

            <!-- Penggunaan Lab Export -->
            <div class="panel-card p-8">
                <h3 class="text-lg font-black text-slate-900 mb-6 flex items-center">
                    <svg class="w-5 h-5 mr-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                    Export Laporan Penggunaan Lab (Excel)
                </h3>
                <form method="GET" action="{{ route('reports.usage.export') }}" class="flex flex-wrap items-end gap-4" target="_blank">
                    <div>
                        <label class="block text-xs font-black text-slate-700 uppercase tracking-widest mb-2">Tanggal Mulai</label>
                        <input type="date" name="start_date" value="{{ old('start_date', $defaultStart) }}"
                               class="rounded-lg border-slate-300 text-sm focus:border-purple-500 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-700 uppercase tracking-widest mb-2">Tanggal Akhir</label>
                        <input type="date" name="end_date" value="{{ old('end_date', $defaultEnd) }}"
                               class="rounded-lg border-slate-300 text-sm focus:border-purple-500 focus:ring-purple-500">
                    </div>
                    <div>
                        <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-purple-600 text-white font-black rounded-lg hover:bg-purple-700 transition text-xs uppercase tracking-widest">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            Export Excel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
