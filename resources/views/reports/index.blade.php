<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold font-bold text-2xl text-slate-900 leading-tight">
            {{ __('Laporan') }}
        </h2>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="panel-shell">
            <!-- Report Overview Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- Borrow Records Stats -->
                <div class="panel-card p-8">
                    <h3 class="text-lg font-black text-slate-900 mb-6 flex items-center">
                        <svg class="w-5 h-5 mr-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                        Laporan Peminjaman Alat & Barang
                    </h3>
                    <div class="grid grid-cols-3 gap-4 mb-6">
                        <div class="bg-emerald-50 rounded-lg p-4">
                            <div class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">Hari Ini</div>
                            <div class="font-bold text-3xl font-bold text-emerald-600">{{ $borrowToday }}</div>
                        </div>
                        <div class="bg-blue-50 rounded-lg p-4">
                            <div class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">Minggu Ini</div>
                            <div class="font-bold text-3xl font-bold text-blue-600">{{ $borrowWeek }}</div>
                        </div>
                        <div class="bg-cyan-50 rounded-lg p-4">
                            <div class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">Bulan Ini</div>
                            <div class="font-bold text-3xl font-bold text-cyan-600">{{ $borrowMonth }}</div>
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-3">
                        <a href="{{ route('reports.borrow.daily') }}" class="inline-flex items-center justify-center px-4 py-3 bg-emerald-600 text-white font-black rounded-lg hover:bg-emerald-700 transition text-xs uppercase tracking-widest">
                            Laporan Harian
                        </a>
                        <a href="{{ route('reports.borrow.weekly') }}" class="inline-flex items-center justify-center px-4 py-3 bg-blue-600 text-white font-black rounded-lg hover:bg-blue-700 transition text-xs uppercase tracking-widest">
                            Laporan Mingguan
                        </a>
                        <a href="{{ route('reports.borrow.monthly') }}" class="inline-flex items-center justify-center px-4 py-3 bg-cyan-600 text-white font-black rounded-lg hover:bg-cyan-700 transition text-xs uppercase tracking-widest">
                            Laporan Bulanan
                        </a>
                    </div>
                </div>

                <!-- Lab Usage Stats -->
                <div class="panel-card p-8">
                    <h3 class="text-lg font-black text-slate-900 mb-6 flex items-center">
                        <svg class="w-5 h-5 mr-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                        Laporan Penggunaan Lab
                    </h3>
                    <div class="grid grid-cols-3 gap-4 mb-6">
                        <div class="bg-purple-50 rounded-lg p-4">
                            <div class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">Hari Ini</div>
                            <div class="font-bold text-3xl font-bold text-purple-600">{{ $usageToday }}</div>
                        </div>
                        <div class="bg-pink-50 rounded-lg p-4">
                            <div class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">Minggu Ini</div>
                            <div class="font-bold text-3xl font-bold text-pink-600">{{ $usageWeek }}</div>
                        </div>
                        <div class="bg-rose-50 rounded-lg p-4">
                            <div class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">Bulan Ini</div>
                            <div class="font-bold text-3xl font-bold text-rose-600">{{ $usageMonth }}</div>
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-3">
                        <a href="{{ route('reports.usage.daily') }}" class="inline-flex items-center justify-center px-4 py-3 bg-purple-600 text-white font-black rounded-lg hover:bg-purple-700 transition text-xs uppercase tracking-widest">
                            Laporan Harian
                        </a>
                        <a href="{{ route('reports.usage.weekly') }}" class="inline-flex items-center justify-center px-4 py-3 bg-pink-600 text-white font-black rounded-lg hover:bg-pink-700 transition text-xs uppercase tracking-widest">
                            Laporan Mingguan
                        </a>
                        <a href="{{ route('reports.usage.monthly') }}" class="inline-flex items-center justify-center px-4 py-3 bg-rose-600 text-white font-black rounded-lg hover:bg-rose-700 transition text-xs uppercase tracking-widest">
                            Laporan Bulanan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>