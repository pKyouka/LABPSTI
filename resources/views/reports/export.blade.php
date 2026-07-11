<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-4">
            <h2 class="text-lg font-semibold text-neutral-800">{{ __('Export Excel Laporan') }}</h2>
            <a href="{{ route('reports.index') }}" class="fluent-btn fluent-btn-secondary fluent-btn-sm">← Kembali</a>
        </div>
    </x-slot>

    <div class="space-y-6 max-w-xl">
        <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm p-6">
            <h3 class="text-base font-semibold text-neutral-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                Export Laporan Peminjaman (Excel)
            </h3>
            <form method="GET" action="{{ route('reports.borrow.export') }}" class="flex flex-wrap items-end gap-3" target="_blank">
                <div><x-input-label for="bs" value="Tanggal Mulai" /><x-text-input id="bs" type="date" name="start_date" :value="old('start_date', $defaultStart)" /></div>
                <div><x-input-label for="be" value="Tanggal Akhir" /><x-text-input id="be" type="date" name="end_date" :value="old('end_date', $defaultEnd)" /></div>
                <div><x-primary-button type="submit" size="sm">Export Excel</x-primary-button></div>
            </form>
        </div>

        <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm p-6">
            <h3 class="text-base font-semibold text-neutral-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                Export Laporan Penggunaan Lab (Excel)
            </h3>
            <form method="GET" action="{{ route('reports.usage.export') }}" class="flex flex-wrap items-end gap-3" target="_blank">
                <div><x-input-label for="us" value="Tanggal Mulai" /><x-text-input id="us" type="date" name="start_date" :value="old('start_date', $defaultStart)" /></div>
                <div><x-input-label for="ue" value="Tanggal Akhir" /><x-text-input id="ue" type="date" name="end_date" :value="old('end_date', $defaultEnd)" /></div>
                <div><x-primary-button type="submit" size="sm">Export Excel</x-primary-button></div>
            </form>
        </div>
    </div>
</x-app-layout>