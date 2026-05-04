<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>List Inventaris - Sistem Lab</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-slate-50 text-slate-900">
    <div class="bg-blue-900 text-white py-2 text-xs font-medium">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center">
            <div>:: C O R E V A L U E :: Akhlak Mulia, Amanah, Profesional, Kolaboratif, Unggul</div>
            <div class="hidden md:block italic">"Health Information Technology, Empowering Healthier Life."</div>
        </div>
    </div>

    <nav class="bg-white/80 backdrop-blur-md shadow-sm sticky top-0 z-50 border-b border-blue-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <a href="{{ route('landing') }}" class="flex flex-col">
                    <span class="text-xl font-black text-blue-900 leading-tight tracking-tighter">LAB<span class="text-blue-600">PSTI</span></span>
                    <span class="text-[10px] font-bold text-slate-500 tracking-[0.2em] uppercase">UNISA YOGYAKARTA</span>
                </a>
                <div class="flex items-center gap-3">
                    <a href="{{ route('landing') }}" class="inline-flex items-center px-5 py-2 border-2 border-slate-200 text-sm font-bold rounded-full text-slate-700 hover:bg-slate-100 transition">Kembali ke Beranda</a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-5 py-2 bg-blue-600 text-sm font-bold rounded-full text-white hover:bg-blue-700 transition">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center px-5 py-2 border-2 border-blue-600 text-sm font-bold rounded-full text-blue-600 hover:bg-blue-600 hover:text-white transition">Login</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h1 class="text-4xl md:text-5xl font-black text-slate-900 tracking-tight">List Inventaris Barang</h1>
                <p class="mt-3 text-slate-500">Semua barang yang tersedia untuk dipinjam dapat dilihat tanpa login.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-10">
                <div class="relative overflow-hidden bg-white rounded-3xl border border-blue-100 p-6 shadow-sm">
                    <div class="absolute -right-7 -top-7 w-20 h-20 rounded-full bg-blue-100"></div>
                    <div class="relative">
                        <div class="text-xs font-black uppercase tracking-widest text-slate-500">Item Tersedia</div>
                        <div class="mt-3 text-4xl font-black text-blue-700">{{ $summary['items_count'] }}</div>
                        <div class="mt-2 text-xs font-semibold text-slate-500">Jumlah barang aktif siap pinjam</div>
                    </div>
                </div>

                <div class="relative overflow-hidden bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-3xl border border-emerald-400 p-6 shadow-sm text-white">
                    <div class="text-xs font-black uppercase tracking-widest text-emerald-100">Stok Siap Pinjam</div>
                    <div class="mt-3 text-4xl font-black">{{ $summary['available_stock_total'] }}</div>
                    <div class="mt-2 text-xs font-semibold text-emerald-100">Total unit tersedia sekarang</div>
                </div>

                <div class="relative overflow-hidden bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
                    <div class="absolute -left-10 -bottom-10 w-24 h-24 rounded-full bg-slate-100"></div>
                    <div class="relative">
                        <div class="text-xs font-black uppercase tracking-widest text-slate-500">Total Seluruh Stok</div>
                        <div class="mt-3 text-4xl font-black text-slate-900">{{ $summary['overall_stock_total'] }}</div>
                        <div class="mt-2 text-xs font-semibold text-slate-500">Akumulasi total unit inventaris</div>
                    </div>
                </div>

                <div class="relative overflow-hidden bg-gradient-to-br from-indigo-500 to-blue-600 rounded-3xl border border-indigo-400 p-6 shadow-sm text-white">
                    <div class="text-xs font-black uppercase tracking-widest text-indigo-100">Kategori Barang</div>
                    <div class="mt-3 text-4xl font-black">{{ $summary['categories_count'] }}</div>
                    <div class="mt-2 text-xs font-semibold text-indigo-100">Jenis kategori yang tersedia</div>
                </div>
            </div>

            @if($inventoryItems->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($inventoryItems as $item)
                        <article class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm hover:shadow-md transition">
                            <div class="flex items-start justify-between gap-3 mb-3">
                                <h2 class="text-xl font-black text-slate-900 leading-tight">{{ $item->name }}</h2>
                                <span class="shrink-0 text-[11px] font-bold text-slate-500 uppercase bg-slate-100 rounded-full px-2 py-1">{{ $item->unit }}</span>
                            </div>

                            <div class="text-xs font-bold uppercase tracking-wider text-blue-600 mb-2">{{ $item->category ?? 'Tanpa Kategori' }}</div>

                            <p class="text-sm text-slate-600 leading-relaxed line-clamp-3">
                                {{ $item->description ?: 'Tidak ada deskripsi barang.' }}
                            </p>

                            <div class="mt-4 rounded-xl bg-slate-50 border border-slate-100 p-3">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-semibold text-slate-500">Stok tersedia</span>
                                    <span class="text-sm font-black text-blue-700">{{ $item->available_stock }} / {{ $item->total_stock }}</span>
                                </div>
                                <div class="h-2 rounded-full bg-slate-200 overflow-hidden">
                                    @php
                                        $stockPercent = $item->total_stock > 0 ? min(100, round(($item->available_stock / $item->total_stock) * 100)) : 0;
                                    @endphp
                                    <div class="h-full rounded-full bg-blue-600" style="width: {{ $stockPercent }}%"></div>
                                </div>
                            </div>

                            <div class="mt-4 grid grid-cols-2 gap-3 text-xs">
                                <div class="rounded-lg bg-slate-50 border border-slate-100 p-2">
                                    <div class="font-semibold text-slate-500">Kondisi</div>
                                    <div class="font-black text-slate-800 mt-1">{{ $item->condition ?: '-' }}</div>
                                </div>
                                <div class="rounded-lg bg-slate-50 border border-slate-100 p-2">
                                    <div class="font-semibold text-slate-500">Lokasi</div>
                                    <div class="font-black text-slate-800 mt-1">{{ $item->location ?: '-' }}</div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="mt-10">
                    {{ $inventoryItems->links() }}
                </div>
            @else
                <div class="text-center py-16 bg-white rounded-3xl border border-slate-100">
                    <p class="text-slate-400 font-bold text-lg">Tidak ada barang yang tersedia untuk dipinjam saat ini.</p>
                </div>
            @endif
        </div>
    </section>
</body>
</html>
