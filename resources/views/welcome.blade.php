<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem Lab - Data Engineering & Digital Forensik</title>
    @include('partials.favicon')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="antialiased bg-slate-50 text-slate-900">
    <!-- Top Bar -->
    <div class="bg-blue-900 text-white py-2 text-xs font-medium">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center">
            <div>
                :: C O R E V A L U E :: Akhlak Mulia, Amanah, Profesional, Kolaboratif, Unggul
            </div>
            <div class="hidden md:block italic">
                “Health Information Technology, Empowering Healthier Life..”
            </div>
        </div>
    </div>

    <!-- Navbar -->
    <nav class="bg-white/80 backdrop-blur-md shadow-sm sticky top-0 z-50 border-b border-blue-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ route('landing') }}" class="flex flex-col">
                        <span class="text-xl font-black text-blue-900 leading-tight tracking-tighter">LAB<span class="text-blue-600">PSTI</span></span>
                        <span class="text-[10px] font-bold text-slate-500 tracking-[0.2em] uppercase">UNISA YOGYAKARTA</span>
                    </a>
                </div>
                <div class="hidden md:flex md:space-x-8">
                    <div class="relative group">
                        <button class="text-slate-700 hover:text-blue-600 px-3 py-2 text-sm font-bold transition flex items-center">
                            Laboratorium <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div class="absolute left-0 mt-2 w-56 bg-white border border-slate-100 shadow-xl rounded-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50">
                            @foreach($categories as $cat)
                                <a href="{{ route('lab.show', $cat->id) }}" class="block px-4 py-3 text-sm text-slate-700 hover:bg-blue-50 hover:text-blue-700 transition first:rounded-t-xl last:rounded-b-xl">
                                    {{ $cat->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                    <a href="#inventory" class="text-slate-700 hover:text-blue-600 px-3 py-2 text-sm font-bold transition">List Inventaris</a>
                    <a href="#bulletins" class="text-slate-700 hover:text-blue-600 px-3 py-2 text-sm font-bold transition">Kabar Berita</a>
                    <a href="#stats" class="text-slate-700 hover:text-blue-600 px-3 py-2 text-sm font-bold transition">Statistik</a>
                </div>
                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="inline-flex items-center px-4 py-2 bg-blue-50 text-sm font-bold rounded-full text-blue-700 hover:bg-blue-100 transition">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center px-6 py-2 border-2 border-blue-600 text-sm font-bold rounded-full text-blue-600 hover:bg-blue-600 hover:text-white transition">LOGIN</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="relative bg-white py-24 lg:py-32 overflow-hidden">
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 bg-blue-50 rounded-full blur-3xl opacity-50"></div>
        <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 bg-blue-100 rounded-full blur-3xl opacity-30"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="lg:flex lg:items-center lg:justify-between">
                <div class="lg:w-1/2 text-center lg:text-left">
                    <span class="inline-block px-4 py-1.5 bg-blue-100 text-blue-700 text-xs font-black rounded-full mb-6 tracking-widest uppercase">PSTI UNISA YOGYAKARTA</span>
                    <h1 class="text-5xl font-black text-slate-900 sm:text-6xl lg:text-7xl leading-[1.1] tracking-tight">
                        Inovasi <span class="text-blue-600">Teknologi</span> Untuk Kesehatan.
                    </h1>
                    <p class="mt-8 text-xl text-slate-600 leading-relaxed max-w-xl mx-auto lg:mx-0">
                        Pusat riset dan pencatatan aktivitas Laboratorium Data Engineering & Digital Forensik dengan basis Nilai-Nilai Islam Berkemajuan.
                    </p>
                    <div class="mt-12 flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        <a href="#bulletins" class="px-10 py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-full shadow-lg shadow-blue-200 transition transform hover:-translate-y-1 text-center">Eksplorasi Berita</a>
                        <a href="#info" class="px-10 py-4 bg-white border-2 border-slate-100 hover:border-blue-200 text-slate-900 font-bold rounded-full shadow-sm transition transform hover:-translate-y-1 text-center">Laboratorium</a>
                    </div>
                </div>
                <div class="hidden lg:block lg:w-5/12">
                    <div class="relative">
                        <div class="absolute inset-0 bg-blue-600 rounded-3xl rotate-3 opacity-10"></div>
                        <div class="relative bg-white p-4 rounded-3xl shadow-2xl border border-slate-100">
                            <div class="bg-slate-50 rounded-2xl h-80 flex items-center justify-center">
                                <svg class="w-32 h-32 text-blue-200" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7 2a1 1 0 00-.707 1.707L14.586 11H3a1 1 0 100 2h11.586l-8.293 8.293a1 1 0 101.414 1.414l10-10a1 1 0 000-1.414l-10-10A1 1 0 007 2z" clip-rule="evenodd"></path></svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Lab Information Section -->
    <section id="info" class="py-24 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-20">
                <h2 class="text-4xl font-black text-slate-900">Laboratorium Kami</h2>
                <p class="mt-4 text-slate-500 font-medium">Fokus pada integrasi Teknologi Informasi dan Bidang Kesehatan</p>
                <div class="mt-6 h-1.5 w-24 bg-blue-600 mx-auto rounded-full"></div>
            </div>
            <div class="grid grid-cols-1 gap-10 md:grid-cols-2">
                @foreach($categories as $cat)
                    <div class="group bg-white p-10 rounded-[2.5rem] shadow-sm border border-slate-100 hover:shadow-2xl hover:border-blue-100 transition-all duration-500">
                        <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center mb-8 group-hover:bg-blue-600 transition-colors duration-500">
                            @if($cat->name == 'Data Engineering')
                                <svg class="w-8 h-8 text-blue-600 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            @else
                                <svg class="w-8 h-8 text-blue-600 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            @endif
                        </div>
                        <h3 class="text-3xl font-black text-slate-900 mb-6 group-hover:text-blue-600 transition-colors">{{ $cat->name }}</h3>
                        <p class="text-slate-600 leading-relaxed mb-8">
                            @if($cat->name == 'Data Engineering')
                                Menerapkan disiplin Ilmu Teknologi Informasi untuk perencanaan ide inovasi teknologi yang bercirikan kesehatan.
                            @else
                                Berfokus pada investigasi bukti digital dan keamanan informasi yang terintegrasi pada bidang ilmu kesehatan.
                            @endif
                        </p>
                        <a href="{{ route('lab.show', $cat->id) }}" class="inline-flex items-center text-blue-600 font-black hover:text-blue-800 transition">
                            Lihat Detail <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Bulletins Section -->
    <section id="bulletins" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center mb-16 gap-6">
                <div class="text-center md:text-left">
                    <h2 class="text-4xl font-black text-slate-900">Kabar Berita</h2>
                    <p class="mt-2 text-slate-500 font-medium italic">Informasi Terbaru & Artikel Teknologi Informasi</p>
                </div>
                <a href="{{ route('landing.bulletins') }}" class="px-8 py-3 border-2 border-slate-100 text-slate-600 font-bold rounded-full hover:bg-slate-50 transition flex items-center">
                    Semua Berita <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>
            <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                @forelse($bulletins as $bulletin)
                    <div class="group flex flex-col bg-white rounded-3xl overflow-hidden border border-slate-100 hover:shadow-2xl hover:-translate-y-2 transition-all duration-500">
                        <a href="{{ route('landing.bulletins.show', $bulletin) }}" class="relative overflow-hidden block">
                            <img class="h-64 w-full object-cover group-hover:scale-110 transition duration-700" src="{{ $bulletin->image_url }}" alt="{{ $bulletin->title }}">
                            <div class="absolute top-6 left-6">
                                <span class="px-4 py-1.5 bg-blue-600 text-white text-[10px] font-black rounded-full uppercase tracking-widest shadow-lg">
                                    {{ $bulletin->category->name }}
                                </span>
                            </div>
                        </a>
                        <div class="p-8 flex-1 flex flex-col">
                            <div class="text-xs font-bold text-slate-400 mb-4 flex items-center">
                                <svg class="w-4 h-4 mr-1.5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                {{ $bulletin->published_at?->format('d M Y') }}
                            </div>
                            <h3 class="text-2xl font-black text-slate-900 mb-4 group-hover:text-blue-600 transition-colors line-clamp-2">
                                <a href="{{ route('landing.bulletins.show', $bulletin) }}">{{ $bulletin->title }}</a>
                            </h3>
                            <div class="text-slate-500 text-sm mb-8 line-clamp-3 leading-relaxed">
                                {!! strip_tags($bulletin->content) !!}
                            </div>
                            <div class="mt-auto pt-6 border-t border-slate-50">
                                <a href="{{ route('landing.bulletins.show', $bulletin) }}" class="inline-flex items-center text-blue-600 font-black text-sm group/btn">
                                    BACA SELENGKAPNYA <svg class="ml-2 w-4 h-4 group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-24 bg-slate-50 rounded-[3rem] border-4 border-dashed border-slate-200">
                        <p class="text-slate-400 font-bold text-lg">Belum ada kabar berita saat ini.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section id="stats" class="py-32 relative overflow-hidden bg-gradient-to-br from-blue-950 via-blue-900 to-sky-900 text-white">
        <div class="absolute -top-24 -left-16 w-80 h-80 bg-sky-400/20 blur-3xl rounded-full"></div>
        <div class="absolute -bottom-24 right-0 w-96 h-96 bg-cyan-300/15 blur-3xl rounded-full"></div>
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_1px_1px,rgba(255,255,255,0.08)_1px,transparent_0)] [background-size:14px_14px] opacity-25"></div>

        @php
            $usageTotal = max(1, (int) $stats['total_usage']);
        @endphp

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-4xl md:text-5xl font-black tracking-tight">Statistik Aktivitas</h2>
                <p class="mt-4 text-blue-100/85 text-sm md:text-base leading-relaxed">Ringkasan penggunaan laboratorium, volume berita, dan proporsi pemakaian tiap lab dalam satu tampilan yang lebih jelas.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch">
                <div class="lg:col-span-4 space-y-6">
                    <div class="rounded-3xl border border-white/15 bg-white/10 backdrop-blur-2xl p-7 shadow-[0_20px_50px_rgba(10,25,70,0.35)]">
                        <div class="text-xs uppercase tracking-[0.22em] font-black text-blue-100/85">Total Penggunaan Lab</div>
                        <div class="mt-4 text-5xl md:text-6xl font-black tracking-tighter text-sky-300">{{ $stats['total_usage'] }}</div>
                        <p class="mt-3 text-xs text-blue-100/75">Total presensi aktivitas praktikum, riset, dan pengerjaan skripsi.</p>
                    </div>

                    <div class="rounded-3xl border border-white/15 bg-white/10 backdrop-blur-2xl p-7 shadow-[0_20px_50px_rgba(10,25,70,0.35)]">
                        <div class="text-xs uppercase tracking-[0.22em] font-black text-blue-100/85">Total Berita</div>
                        <div class="mt-4 text-5xl md:text-6xl font-black tracking-tighter text-cyan-300">{{ $stats['total_bulletins'] }}</div>
                        <p class="mt-3 text-xs text-blue-100/75">Publikasi informasi kegiatan laboratorium Data Engineering dan Digital Forensik.</p>
                    </div>
                </div>

                <div class="lg:col-span-8 rounded-[2rem] border border-white/15 bg-white/10 backdrop-blur-2xl p-6 md:p-8 shadow-[0_30px_70px_rgba(10,25,70,0.4)]">
                    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
                        <h3 class="text-sm md:text-base font-black uppercase tracking-[0.2em] text-blue-100">Proporsi Penggunaan Lab</h3>
                        <span class="text-[11px] uppercase font-black tracking-[0.18em] text-blue-200/90">Total {{ $stats['total_usage'] }} Aktivitas</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                        <div class="h-[280px] md:h-[310px]">
                            <canvas id="landingChart"></canvas>
                        </div>

                        <div class="space-y-4">
                            @foreach($usagePerLab as $item)
                                @php
                                    $isDE = stripos((string) $item['label'], 'data engineering') !== false;
                                    $accentClass = $isDE ? 'bg-sky-400' : 'bg-cyan-300';
                                    $badgeClass = $isDE ? 'text-sky-200' : 'text-cyan-200';
                                    $percent = round((((int) $item['count']) / $usageTotal) * 100, 1);
                                @endphp
                                <div class="rounded-2xl border border-white/10 bg-blue-950/25 p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center gap-2">
                                            <span class="inline-block w-3 h-3 rounded-full {{ $accentClass }}"></span>
                                            <span class="text-sm font-bold text-blue-50">{{ $item['label'] }}</span>
                                        </div>
                                        <div class="text-xs font-black {{ $badgeClass }}">{{ $percent }}%</div>
                                    </div>
                                    <div class="w-full h-2.5 rounded-full bg-white/10 overflow-hidden">
                                        <div class="h-full {{ $accentClass }} rounded-full" style="width: {{ $percent }}%"></div>
                                    </div>
                                    <div class="mt-2 text-xs text-blue-100/75">{{ $item['count'] }} aktivitas tercatat</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Inventory Overview Section -->
    <section id="inventory" class="py-24 bg-slate-50 border-y border-slate-100">
        @php
            $inventoryCount = isset($inventoryItems) ? $inventoryItems->count() : 0;
            $availableStockTotal = isset($inventoryItems) ? (int) $inventoryItems->sum('available_stock') : 0;
            $overallStockTotal = isset($inventoryItems) ? (int) $inventoryItems->sum('total_stock') : 0;
            $inventoryCategories = isset($inventoryItems)
                ? $inventoryItems->pluck('category')->filter()->unique()->count()
                : 0;
        @endphp

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-black text-slate-900">List Inventaris</h2>
                <p class="mt-3 text-slate-500">Ringkasan inventaris untuk pengunjung. Daftar barang lengkap tersedia di halaman khusus agar tampilan tetap rapi.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <div class="relative overflow-hidden bg-white rounded-3xl border border-blue-100 p-6 shadow-sm">
                    <div class="absolute -right-7 -top-7 w-20 h-20 rounded-full bg-blue-100"></div>
                    <div class="relative">
                        <div class="text-xs font-black uppercase tracking-widest text-slate-500">Item Tersedia</div>
                        <div class="mt-3 text-4xl font-black text-blue-700">{{ $inventoryCount }}</div>
                        <div class="mt-2 text-xs font-semibold text-slate-500">Barang aktif yang siap dipinjam</div>
                    </div>
                </div>
                <div class="relative overflow-hidden bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-3xl border border-emerald-400 p-6 shadow-sm text-white">
                    <div class="text-xs font-black uppercase tracking-widest text-emerald-100">Total Stok Siap Pinjam</div>
                    <div class="mt-3 text-4xl font-black">{{ $availableStockTotal }}</div>
                    <div class="mt-2 text-xs font-semibold text-emerald-100">Unit yang tersedia saat ini</div>
                </div>
                <div class="relative overflow-hidden bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
                    <div class="absolute -left-10 -bottom-10 w-24 h-24 rounded-full bg-slate-100"></div>
                    <div class="relative">
                        <div class="text-xs font-black uppercase tracking-widest text-slate-500">Total Seluruh Stok</div>
                        <div class="mt-3 text-4xl font-black text-slate-900">{{ $overallStockTotal }}</div>
                        <div class="mt-2 text-xs font-semibold text-slate-500">Akumulasi seluruh item inventaris</div>
                    </div>
                </div>
                <div class="relative overflow-hidden bg-gradient-to-br from-indigo-500 to-blue-600 rounded-3xl border border-indigo-400 p-6 shadow-sm text-white">
                    <div class="text-xs font-black uppercase tracking-widest text-indigo-100">Kategori Barang</div>
                    <div class="mt-3 text-4xl font-black">{{ $inventoryCategories }}</div>
                    <div class="mt-2 text-xs font-semibold text-indigo-100">Jenis perlengkapan tersedia</div>
                </div>
            </div>

            <div class="mt-8 rounded-3xl bg-white border border-slate-100 p-6 md:p-8 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-5">
                <div>
                    <h3 class="text-xl font-black text-slate-900">Lihat Semua List Inventaris</h3>
                    <p class="mt-1 text-sm text-slate-500">Halaman khusus menampilkan semua barang, stok tersedia, kategori, kondisi, dan lokasi secara lebih rapi.</p>
                </div>
                <a href="{{ route('landing.inventory') }}" class="inline-flex items-center justify-center px-7 py-3 rounded-full bg-blue-600 text-white font-bold hover:bg-blue-700 transition">
                    Buka Halaman Inventaris
                    <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-slate-900 text-white py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-4 gap-16">
            <div class="md:col-span-2">
                <span class="text-3xl font-black text-white tracking-tighter mb-8 block">LAB<span class="text-blue-500">PSTI</span></span>
                <p class="text-slate-400 leading-relaxed text-lg max-w-md">
                    Program Studi Teknologi Informasi (PSTI) Universitas ‘Aisyiyah (UNISA) Yogyakarta menerapkan disiplin Ilmu Teknologi Informasi yang berintegrasi pada bidang ilmu kesehatan.
                </p>
            </div>
            <div>
                <h3 class="text-xs font-black text-blue-500 mb-8 uppercase tracking-[0.2em]">Navigasi</h3>
                <ul class="space-y-4 text-sm font-bold text-slate-300">
                    <li><a href="#info" class="hover:text-blue-400 transition">Halaman Utama</a></li>
                    <li><a href="{{ route('landing.inventory') }}" class="hover:text-blue-400 transition">List Inventaris</a></li>
                    <li><a href="#bulletins" class="hover:text-blue-400 transition">Kabar Berita</a></li>
                    <li><a href="#stats" class="hover:text-blue-400 transition">Statistik</a></li>
                    <li><a href="{{ route('login') }}" class="hover:text-blue-400 transition">Admin Panel</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-xs font-black text-blue-500 mb-8 uppercase tracking-[0.2em]">Hubungi Kami</h3>
                <ul class="space-y-6 text-sm font-bold text-slate-300">
                    <li class="flex items-start">
                        <svg class="w-5 h-5 mr-4 text-blue-500 mt-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <span>Jl. Siliwangi (Ring Road Barat) No. 63, Sleman, Yogyakarta.</span>
                    </li>
                    <li class="flex items-center">
                        <svg class="w-5 h-5 mr-4 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        <span>info@unisayogya.ac.id</span>
                    </li>
                </ul>
            </div>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 border-t border-slate-800 mt-20 pt-10 text-center text-slate-500 text-[10px] font-black uppercase tracking-widest">
            <p>&copy; {{ date('Y') }} PSTI UNISA Yogyakarta. “Health Information Technology, Empowering Healthier Life.”</p>
        </div>
    </footer>

    <script>
        const chartElement = document.getElementById('landingChart');

        if (chartElement) {
            const ctx = chartElement.getContext('2d');
            const usageData = @json($usagePerLab);
            const chartLabels = usageData.map(item => item.label);
            const chartValues = usageData.map(item => Number(item.count));
            const usageTotal = chartValues.reduce((sum, value) => sum + value, 0);

            const centerTextPlugin = {
                id: 'centerTextPlugin',
                afterDraw(chart) {
                    const meta = chart.getDatasetMeta(0);

                    if (!meta || !meta.data || !meta.data.length) {
                        return;
                    }

                    const x = meta.data[0].x;
                    const y = meta.data[0].y;
                    const { ctx } = chart;

                    ctx.save();
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';

                    ctx.fillStyle = 'rgba(219, 234, 254, 0.9)';
                    ctx.font = '800 12px Nunito, sans-serif';
                    ctx.fillText('TOTAL', x, y - 14);

                    ctx.fillStyle = '#7dd3fc';
                    ctx.font = '900 24px Nunito, sans-serif';
                    ctx.fillText(String(usageTotal), x, y + 12);
                    ctx.restore();
                }
            };

            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        data: chartValues,
                        backgroundColor: [
                            'rgba(56, 189, 248, 0.88)',
                            'rgba(34, 211, 238, 0.82)',
                            'rgba(14, 165, 233, 0.72)',
                            'rgba(6, 182, 212, 0.72)',
                        ],
                        borderColor: [
                            'rgba(56, 189, 248, 1)',
                            'rgba(34, 211, 238, 1)',
                            'rgba(14, 165, 233, 1)',
                            'rgba(6, 182, 212, 1)',
                        ],
                        borderWidth: 2,
                        borderRadius: 6,
                        hoverOffset: 10,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '62%',
                    layout: {
                        padding: 8,
                    },
                    plugins: {
                        legend: {
                            display: false,
                        },
                        tooltip: {
                            callbacks: {
                                label(context) {
                                    const value = Number(context.raw || 0);
                                    const percent = usageTotal > 0 ? ((value / usageTotal) * 100).toFixed(1) : '0.0';
                                    return `${context.label}: ${value} aktivitas (${percent}%)`;
                                }
                            }
                        }
                    }
                },
                plugins: [centerTextPlugin],
            });
        }
    </script>
</body>
</html>
