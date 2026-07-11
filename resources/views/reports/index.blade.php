<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold text-neutral-800">
            {{ __('Laporan') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        <!-- Report Overview Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Borrow Records Stats -->
            <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm p-6">
                <h3 class="text-base font-semibold text-neutral-800 mb-5 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                    Laporan Peminjaman
                </h3>
                <div class="grid grid-cols-3 gap-3 mb-5">
                    <div class="bg-emerald-500 rounded-xl p-4 text-center shadow-sm">
                        <p class="text-xs font-bold text-emerald-100 uppercase tracking-wider mb-1">Hari Ini</p>
                        <p class="text-3xl font-bold text-white">{{ $borrowToday }}</p>
                    </div>
                    <div class="bg-blue-500 rounded-xl p-4 text-center shadow-sm">
                        <p class="text-xs font-bold text-blue-100 uppercase tracking-wider mb-1">Minggu</p>
                        <p class="text-3xl font-bold text-white">{{ $borrowWeek }}</p>
                    </div>
                    <div class="bg-cyan-500 rounded-xl p-4 text-center shadow-sm">
                        <p class="text-xs font-bold text-cyan-100 uppercase tracking-wider mb-1">Bulan</p>
                        <p class="text-3xl font-bold text-white">{{ $borrowMonth }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-2">
                    <a href="{{ route('reports.borrow.daily') }}" class="fluent-btn fluent-btn-primary fluent-btn-sm w-full">Harian</a>
                    <a href="{{ route('reports.borrow.weekly') }}" class="fluent-btn fluent-btn-secondary fluent-btn-sm w-full">Mingguan</a>
                    <a href="{{ route('reports.borrow.monthly') }}" class="fluent-btn fluent-btn-secondary fluent-btn-sm w-full">Bulanan</a>
                </div>
            </div>

            <!-- Lab Usage Stats -->
            <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm p-6">
                <h3 class="text-base font-semibold text-neutral-800 mb-5 flex items-center gap-2">
                    <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                    Laporan Penggunaan Lab
                </h3>
                <div class="grid grid-cols-3 gap-3 mb-5">
                    <div class="bg-purple-500 rounded-xl p-4 text-center shadow-sm">
                        <p class="text-xs font-bold text-purple-100 uppercase tracking-wider mb-1">Hari Ini</p>
                        <p class="text-3xl font-bold text-white">{{ $usageToday }}</p>
                    </div>
                    <div class="bg-pink-500 rounded-xl p-4 text-center shadow-sm">
                        <p class="text-xs font-bold text-pink-100 uppercase tracking-wider mb-1">Minggu</p>
                        <p class="text-3xl font-bold text-white">{{ $usageWeek }}</p>
                    </div>
                    <div class="bg-rose-500 rounded-xl p-4 text-center shadow-sm">
                        <p class="text-xs font-bold text-rose-100 uppercase tracking-wider mb-1">Bulan</p>
                        <p class="text-3xl font-bold text-white">{{ $usageMonth }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-2">
                    <a href="{{ route('reports.usage.daily') }}" class="fluent-btn fluent-btn-primary fluent-btn-sm w-full">Harian</a>
                    <a href="{{ route('reports.usage.weekly') }}" class="fluent-btn fluent-btn-secondary fluent-btn-sm w-full">Mingguan</a>
                    <a href="{{ route('reports.usage.monthly') }}" class="fluent-btn fluent-btn-secondary fluent-btn-sm w-full">Bulanan</a>
                </div>
            </div>
        </div>

        <!-- Export -->
        <div class="text-center">
            <a href="{{ route('reports.export') }}" class="fluent-btn fluent-btn-primary fluent-btn-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Export Excel (Rentang Tanggal)
            </a>
        </div>
    </div>
</x-app-layout>