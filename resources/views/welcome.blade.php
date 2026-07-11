<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'LABPSTI') }} — Laboratorium PSTI UNISA</title>
    @include('partials.favicon')
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=roboto:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="antialiased bg-[#F3F3F3] text-[#1F1F1F]">
    <!-- Mica Background -->
    <div aria-hidden="true" class="fixed inset-0 -z-10 fluent-mica"></div>

    <!-- Accent Top Bar -->
    <div aria-hidden="true" class="fixed top-0 inset-x-0 h-0.5 bg-accent z-50"></div>

    <!-- Navbar — Premium Fluent Acrylic -->
    <nav class="sticky top-0 z-40 fluent-topbar border-b border-black/[0.06]" x-data="{ scrolled: false, labsOpen: false }" @scroll.window="scrolled = window.scrollY > 50">
        <div class="max-w-7xl mx-auto px-5 sm:px-6 lg:px-8 flex justify-between h-[54px] items-center">
            <!-- Brand -->
            <a href="{{ route('landing') }}" class="flex items-center gap-2.5 flex-shrink-0 group">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-accent to-accent-800 text-white grid place-items-center flex-shrink-0 shadow-fluent-sm group-hover:shadow-fluent-md group-hover:scale-105 transition-all duration-200">
                    <span class="text-xs font-bold tracking-tight">TI</span>
                </div>
                <div class="hidden sm:block">
                    <span class="text-sm font-semibold text-neutral-800 tracking-tight">LAB<span class="text-accent">PSTI</span></span>
                    <span class="text-[9px] font-semibold text-neutral-400 tracking-[0.12em] uppercase block leading-none">UNISA YOGYAKARTA</span>
                </div>
            </a>

            <!-- Desktop Nav -->
            <div class="hidden md:flex items-center gap-0.5">
                <div class="relative" @mouseenter="labsOpen = true" @mouseleave="labsOpen = false" x-data>
                    <button class="fl-nav-link flex items-center gap-1 px-3 py-2 text-[13px] font-medium text-neutral-600 hover:text-neutral-900 rounded-lg transition">
                        Laboratorium
                        <svg class="w-3 h-3 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="m6 9 6 6 6-6"/></svg>
                    </button>
                    <div x-show="labsOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100" class="absolute top-full left-0 mt-1.5 w-56 fluent-glass rounded-xl overflow-hidden z-50" style="display:none">
                        @foreach($categories as $cat)
                            <a href="{{ route('lab.show', $cat->id) }}" class="block px-4 py-3 text-[13px] font-medium text-neutral-600 hover:bg-accent-light hover:text-accent transition first:rounded-t-xl last:rounded-b-xl">
                                <span class="flex items-center gap-2.5">
                                    @if($cat->name == 'Data Engineering')
                                        <svg class="w-4 h-4 text-accent/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                    @else
                                        <svg class="w-4 h-4 text-accent/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                    @endif
                                    {{ $cat->name }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
                <a href="#inventory" class="fl-nav-link px-3 py-2 text-[13px] font-medium text-neutral-600 hover:text-neutral-900 rounded-lg transition">Inventaris</a>
                <a href="#bulletins" class="fl-nav-link px-3 py-2 text-[13px] font-medium text-neutral-600 hover:text-neutral-900 rounded-lg transition">Kabar Berita</a>
                <a href="#stats" class="fl-nav-link px-3 py-2 text-[13px] font-medium text-neutral-600 hover:text-neutral-900 rounded-lg transition">Statistik</a>
            </div>

            <div class="flex items-center gap-2.5">
                @auth
                    <a href="{{ route('dashboard') }}" class="fluent-btn fluent-btn-primary fluent-btn-sm !px-4">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="fluent-btn fluent-btn-sm !px-4 font-medium text-neutral-700 bg-black/[0.06] border border-neutral-200/70 hover:bg-black/[0.09] rounded-lg transition">Login</a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- HERO — Premium Fluent -->
    <section class="fluent-hero pt-20 pb-16 lg:pt-28 lg:pb-24">
        <!-- Glow Orbs -->
        <div class="fluent-glow fluent-glow-accent w-[500px] h-[500px] -top-40 -right-32"></div>
        <div class="fluent-glow fluent-glow-purple w-[400px] h-[400px] -bottom-32 -left-24"></div>
        <div class="fluent-glow fluent-glow-teal w-[300px] h-[300px] top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 opacity-40"></div>

        <div class="relative max-w-7xl mx-auto px-5 sm:px-6 lg:px-8">
            <div class="lg:flex lg:items-center lg:justify-between gap-16">
                <div class="lg:w-[55%] text-center lg:text-left">
                    <div class="fluent-section-pill mb-6">{{ config('app.name', 'LABPSTI') }} UNISA YOGYAKARTA</div>
                    <h1 class="text-[2.75rem] sm:text-6xl lg:text-[4rem] font-semibold text-neutral-900 leading-[1.08] tracking-tight">
                        Inovasi <span class="text-accent">Teknologi</span><br class="hidden sm:block"> Untuk Kesehatan
                    </h1>
                    <p class="mt-6 text-base lg:text-lg text-neutral-500 leading-relaxed max-w-xl mx-auto lg:mx-0">
                        Pusat riset dan pencatatan aktivitas Laboratorium <strong class="text-neutral-700">Data Engineering</strong> & <strong class="text-neutral-700">Digital Forensik</strong> dengan basis Nilai-Nilai Islam Berkemajuan.
                    </p>
                    <div class="mt-9 flex flex-col sm:flex-row gap-3.5 justify-center lg:justify-start">
                        <a href="#bulletins" class="fluent-btn fluent-btn-primary fluent-btn-lg !rounded-xl shadow-lg shadow-accent/20 hover:shadow-xl hover:shadow-accent/25 transition-shadow">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                            Eksplorasi Berita
                        </a>
                        <a href="#info" class="fluent-btn fluent-btn-secondary fluent-btn-lg !rounded-xl">
                            Laboratorium
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>

                <!-- Hero Visual — Floating Glass Card -->
                <div class="hidden lg:block lg:w-[40%] mt-12 lg:mt-0">
                    <div class="relative" style="perspective: 800px;">
                        <div class="fluent-glass rounded-2xl p-5 transform rotate-y-[-4deg] rotate-x-[2deg] hover:rotate-y-0 hover:rotate-x-0 transition-transform duration-500">
                            <div class="bg-gradient-to-br from-accent-50 to-surface rounded-xl h-[300px] flex items-center justify-center relative overflow-hidden">
                                <div class="absolute inset-0 bg-[radial-gradient(circle_at_1px_1px,rgba(0,120,212,0.06)_1px,transparent_0)] [background-size:16px_16px]"></div>
                                <div class="relative z-10 text-center">
                                    <div class="w-20 h-20 mx-auto rounded-2xl bg-gradient-to-br from-accent to-accent-800 text-white grid place-items-center shadow-fluent-lg mb-5 ring-4 ring-accent/10">
                                        <span class="text-2xl font-bold tracking-tight">TI</span>
                                    </div>
                                    <p class="text-lg font-semibold text-neutral-800">LAB<span class="text-accent">PSTI</span></p>
                                    <p class="text-xs text-neutral-400 mt-1.5">Sistem Informasi Laboratorium</p>
                                    <div class="mt-6 inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-700 text-[11px] font-semibold rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                        Online & Siap Digunakan
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Reflection shadow -->
                        <div class="absolute -bottom-4 left-4 right-4 h-8 bg-accent/10 blur-2xl rounded-full"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- LABS — Premium Cards -->
    <section id="info" class="py-20 relative">
        <div class="max-w-7xl mx-auto px-5 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <div class="fluent-section-pill mb-4">Fasilitas</div>
                <h2 class="fluent-section-heading">Laboratorium Kami</h2>
                <p class="fluent-section-sub mt-3 mx-auto text-center">Fokus pada integrasi Teknologi Informasi dan Bidang Kesehatan</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($categories as $cat)
                    <div class="fluent-glow-border group fluent-card p-8">
                        <div class="w-14 h-14 rounded-2xl bg-accent-light flex items-center justify-center mb-6 group-hover:bg-accent transition-all duration-300 group-hover:shadow-lg group-hover:shadow-accent/20">
                            @if($cat->name == 'Data Engineering')
                                <svg class="w-7 h-7 text-accent group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            @else
                                <svg class="w-7 h-7 text-accent group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            @endif
                        </div>
                        <h3 class="text-2xl font-semibold text-neutral-800 mb-3 group-hover:text-accent transition-colors">{{ $cat->name }}</h3>
                        <p class="text-[15px] text-neutral-500 leading-relaxed mb-7">
                            @if($cat->name == 'Data Engineering')
                                Menerapkan disiplin Ilmu Teknologi Informasi untuk perencanaan ide inovasi teknologi yang bercirikan kesehatan.
                            @else
                                Berfokus pada investigasi bukti digital dan keamanan informasi yang terintegrasi pada bidang ilmu kesehatan.
                            @endif
                        </p>
                        <a href="{{ route('lab.show', $cat->id) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-accent hover:text-accent-hover transition group/link">
                            Lihat Detail
                            <svg class="w-4 h-4 group-hover/link:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- BULLETINS — Premium Grid -->
    <section id="bulletins" class="py-20 relative bg-white/50">
        <div class="fluent-glow fluent-glow-accent w-[400px] h-[400px] top-20 -right-40 opacity-30"></div>

        <div class="relative max-w-7xl mx-auto px-5 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-end mb-14 gap-4">
                <div>
                    <div class="fluent-section-pill mb-4">Informasi</div>
                    <h2 class="fluent-section-heading">Kabar Berita</h2>
                    <p class="fluent-section-sub mt-2">Informasi Terbaru & Artikel Teknologi Informasi</p>
                </div>
                <a href="{{ route('landing.bulletins') }}" class="fluent-btn fluent-btn-accent-outline fluent-btn-md !rounded-lg shrink-0">
                    Semua Berita
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @forelse($bulletins as $bulletin)
                    <article class="fluent-glow-border group flex flex-col fluent-card overflow-hidden">
                        <a href="{{ route('landing.bulletins.show', $bulletin) }}" class="relative overflow-hidden block">
                            <img class="h-56 w-full object-cover group-hover:scale-105 transition duration-600" src="{{ $bulletin->image_url }}" alt="{{ $bulletin->title }}">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/30 via-transparent to-transparent"></div>
                            <div class="absolute top-4 left-4">
                                <span class="fluent-badge fluent-badge-info shadow-sm backdrop-blur-sm bg-accent-light/90">{{ $bulletin->category->name }}</span>
                            </div>
                        </a>
                        <div class="p-5 flex-1 flex flex-col">
                            <div class="flex items-center gap-2 text-[11px] font-medium text-neutral-400 mb-3">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                {{ $bulletin->published_at?->format('d M Y') }}
                            </div>
                            <h3 class="text-base font-semibold text-neutral-800 mb-2.5 line-clamp-2 group-hover:text-accent transition-colors leading-snug">
                                <a href="{{ route('landing.bulletins.show', $bulletin) }}">{{ $bulletin->title }}</a>
                            </h3>
                            <p class="text-[13px] text-neutral-500 mb-5 line-clamp-3 leading-relaxed">
                                {!! strip_tags($bulletin->content) !!}
                            </p>
                            <div class="mt-auto pt-4 border-t border-neutral-100">
                                <a href="{{ route('landing.bulletins.show', $bulletin) }}" class="inline-flex items-center gap-1.5 text-[13px] font-semibold text-accent hover:text-accent-hover transition group/btn">
                                    Baca Selengkapnya
                                    <svg class="w-3.5 h-3.5 group-hover/btn:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="col-span-3 text-center py-24 bg-white rounded-2xl border-2 border-dashed border-neutral-200">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-neutral-100 grid place-items-center">
                            <svg class="w-7 h-7 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                        </div>
                        <p class="text-neutral-400 font-medium text-base">Belum ada kabar berita saat ini.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- STATS — Premium Dark Section -->
    <section id="stats" class="py-24 relative overflow-hidden bg-gradient-to-br from-[#0F172A] via-[#1E293B] to-[#0C4A6E] text-white">
        <!-- Dot Grid -->
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_1px_1px,rgba(255,255,255,0.06)_1px,transparent_0)] [background-size:12px_12px] opacity-40"></div>
        <!-- Glow Orbs -->
        <div class="fluent-glow fluent-glow-accent w-[600px] h-[600px] -top-60 -left-40" style="background: rgba(56,189,248,0.18);"></div>
        <div class="fluent-glow fluent-glow-purple w-[400px] h-[400px] -bottom-40 -right-20" style="background: rgba(139,92,246,0.12);"></div>

        @php $usageTotal = max(1, (int) $stats['total_usage']); @endphp

        <div class="relative max-w-7xl mx-auto px-5 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <div class="fluent-section-pill !bg-white/15 !text-white backdrop-blur-md mb-4 font-semibold">Analitik</div>
                <h2 class="text-[2.25rem] font-semibold tracking-tight text-white">Statistik Aktivitas</h2>
                <p class="mt-3 text-base text-white/80 leading-relaxed">Ringkasan penggunaan laboratorium, volume berita, dan proporsi pemakaian tiap lab.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
                <!-- Stat Cards Left -->
                <div class="lg:col-span-4 space-y-5">
                    <div class="rounded-2xl p-7 bg-white/10 border border-white/15 backdrop-blur-md">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 rounded-xl bg-sky-400/25 flex items-center justify-center">
                                <svg class="w-5 h-5 text-sky-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <p class="text-xs font-bold uppercase tracking-[0.15em] text-white/90">Total Penggunaan Lab</p>
                        </div>
                        <p class="text-5xl font-bold tracking-tight text-white">{{ $stats['total_usage'] }}</p>
                        <p class="mt-2 text-sm text-white/70">Total presensi aktivitas praktikum, riset, dan pengerjaan skripsi.</p>
                    </div>

                    <div class="rounded-2xl p-7 bg-white/10 border border-white/15 backdrop-blur-md">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 rounded-xl bg-emerald-400/25 flex items-center justify-center">
                                <svg class="w-5 h-5 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                            </div>
                            <p class="text-xs font-bold uppercase tracking-[0.15em] text-white/90">Total Berita</p>
                        </div>
                        <p class="text-5xl font-bold tracking-tight text-white">{{ $stats['total_bulletins'] }}</p>
                        <p class="mt-2 text-sm text-white/70">Publikasi informasi kegiatan laboratorium DE dan DF.</p>
                    </div>
                </div>

                <!-- Chart -->
                <div class="lg:col-span-8 rounded-2xl p-7 bg-white/10 border border-white/15 backdrop-blur-md">
                    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
                        <h3 class="text-base font-bold uppercase tracking-[0.12em] text-white">Proporsi Penggunaan Lab</h3>
                        <span class="text-xs font-bold text-white bg-white/15 px-3 py-1.5 rounded-full">Total {{ $stats['total_usage'] }} Aktivitas</span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-7 items-center">
                        <div class="h-[270px] flex items-center justify-center">
                            <canvas id="landingChart"></canvas>
                        </div>
                        <div class="space-y-3.5">
                            @foreach($usagePerLab as $item)
                                @php
                                    $isDE = stripos((string) $item['label'], 'data engineering') !== false;
                                    $accentClass = $isDE ? 'bg-sky-400' : 'bg-cyan-400';
                                    $badgeClass = $isDE ? 'text-sky-300' : 'text-cyan-300';
                                    $borderClass = $isDE ? 'border-sky-400/30' : 'border-cyan-400/30';
                                    $bgClass = $isDE ? 'bg-sky-400/10' : 'bg-cyan-400/10';
                                    $percent = round((((int) $item['count']) / $usageTotal) * 100, 1);
                                @endphp
                                <div class="rounded-xl border p-4 {{ $bgClass }} {{ $borderClass }}">
                                    <div class="flex items-center justify-between mb-2.5">
                                        <div class="flex items-center gap-2.5">
                                            <span class="inline-block w-3 h-3 rounded-full {{ $accentClass }} shadow-sm shadow-white/20"></span>
                                            <span class="text-sm font-semibold text-white">{{ $item['label'] }}</span>
                                        </div>
                                        <span class="text-sm font-bold {{ $badgeClass }} tabular-nums">{{ $percent }}%</span>
                                    </div>
                                    <div class="w-full h-3 rounded-full bg-white/10 overflow-hidden">
                                        <div class="h-full {{ $accentClass }} rounded-full transition-all duration-700 ease-out shadow-sm" style="width: 0%" data-width="{{ $percent }}%"></div>
                                    </div>
                                    <p class="mt-2 text-xs text-white/60 font-semibold">{{ $item['count'] }} aktivitas</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- INVENTORY — Premium Cards -->
    <section id="inventory" class="py-20 relative">
        <div class="fluent-glow fluent-glow-teal w-[350px] h-[350px] -bottom-24 -left-32 opacity-30"></div>

        @php
            $inventoryCount = isset($inventoryItems) ? $inventoryItems->count() : 0;
            $availableStockTotal = isset($inventoryItems) ? (int) $inventoryItems->sum('available_stock') : 0;
            $overallStockTotal = isset($inventoryItems) ? (int) $inventoryItems->sum('total_stock') : 0;
            $inventoryCategories = isset($inventoryItems) ? $inventoryItems->pluck('category')->filter()->unique()->count() : 0;
        @endphp

        <div class="relative max-w-7xl mx-auto px-5 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <div class="fluent-section-pill mb-4">Perlengkapan</div>
                <h2 class="fluent-section-heading">List Inventaris</h2>
                <p class="fluent-section-sub mt-3 mx-auto text-center">Ringkasan inventaris untuk pengunjung. Daftar lengkap tersedia di halaman khusus.</p>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
                <div class="fluent-stat-card text-center">
                    <div class="w-10 h-10 mx-auto mb-3 rounded-xl bg-accent-light flex items-center justify-center">
                        <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </div>
                    <p class="text-3xl font-bold text-accent">{{ $inventoryCount }}</p>
                    <p class="text-[11px] font-medium text-neutral-400 mt-1">Item Tersedia</p>
                </div>
                <div class="fluent-stat-card !bg-gradient-to-br !from-emerald-500 !to-emerald-600 !text-white !border-emerald-400/30 text-center">
                    <p class="text-3xl font-bold">{{ $availableStockTotal }}</p>
                    <p class="text-[11px] font-medium text-emerald-100 mt-1">Stok Siap Pinjam</p>
                </div>
                <div class="fluent-stat-card text-center">
                    <p class="text-3xl font-bold text-neutral-800">{{ $overallStockTotal }}</p>
                    <p class="text-[11px] font-medium text-neutral-400 mt-1">Total Seluruh Stok</p>
                </div>
                <div class="fluent-stat-card !bg-gradient-to-br !from-accent-700 !to-accent !text-white !border-accent-400/30 text-center">
                    <p class="text-3xl font-bold">{{ $inventoryCategories }}</p>
                    <p class="text-[11px] font-medium text-accent-100 mt-1">Kategori Barang</p>
                </div>
            </div>

            <div class="fluent-card p-6 flex flex-col sm:flex-row items-center justify-between gap-5">
                <div>
                    <h3 class="text-lg font-semibold text-neutral-800">Lihat Semua List Inventaris</h3>
                    <p class="mt-1 text-[14px] text-neutral-500">Halaman khusus menampilkan semua barang, stok, kategori, kondisi, dan lokasi.</p>
                </div>
                <a href="{{ route('landing.inventory') }}" class="fluent-btn fluent-btn-primary fluent-btn-lg !rounded-xl shrink-0">
                    Buka Inventaris
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>
    </section>

    <!-- FOOTER — Premium -->
    <footer class="relative bg-neutral-950 text-white pt-20 pb-12 overflow-hidden">
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top,rgba(0,120,212,0.08)_0%,transparent_60%)]"></div>
        <div class="relative max-w-7xl mx-auto px-5 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-14">
                <div class="md:col-span-2">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-accent to-accent-700 grid place-items-center shadow-lg shadow-accent/20">
                            <span class="text-sm font-bold tracking-tight">TI</span>
                        </div>
                        <span class="text-xl font-semibold tracking-tight">LAB<span class="text-accent">PSTI</span></span>
                    </div>
                    <p class="text-sm text-neutral-400 leading-relaxed max-w-md">
                        Program Studi Teknologi Informasi (PSTI) Universitas 'Aisyiyah (UNISA) Yogyakarta menerapkan disiplin Ilmu Teknologi Informasi yang berintegrasi pada bidang ilmu kesehatan.
                    </p>
                </div>
                <div>
                    <h3 class="text-[11px] font-semibold text-accent mb-6 uppercase tracking-[0.15em]">Navigasi</h3>
                    <ul class="space-y-3 text-[13px] font-medium text-neutral-400">
                        <li><a href="#info" class="hover:text-white transition-colors">Laboratorium</a></li>
                        <li><a href="{{ route('landing.inventory') }}" class="hover:text-white transition-colors">List Inventaris</a></li>
                        <li><a href="#bulletins" class="hover:text-white transition-colors">Kabar Berita</a></li>
                        <li><a href="#stats" class="hover:text-white transition-colors">Statistik</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-white transition-colors">Login</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-[11px] font-semibold text-accent mb-6 uppercase tracking-[0.15em]">Hubungi Kami</h3>
                    <ul class="space-y-5 text-[13px] font-medium text-neutral-400">
                        <li class="flex items-start gap-3">
                            <svg class="w-4 h-4 text-neutral-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span>Jl. Siliwangi (Ring Road Barat) No. 63, Sleman, Yogyakarta.</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <svg class="w-4 h-4 text-neutral-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            <span>info@unisayogya.ac.id</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-neutral-800 mt-14 pt-8 text-center">
                <p class="text-[11px] font-medium text-neutral-500 tracking-[0.12em] uppercase">&copy; {{ date('Y') }} PSTI UNISA Yogyakarta. "Health Information Technology, Empowering Healthier Life."</p>
            </div>
        </div>
    </footer>

    <!-- Back to Top -->
    <button id="backToTop" class="fluent-back-top" onclick="window.scrollTo({top:0,behavior:'smooth'})" aria-label="Kembali ke atas">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
    </button>

    <script>
        // Back to Top
        const backToTop = document.getElementById('backToTop');
        window.addEventListener('scroll', () => {
            backToTop.classList.toggle('show', window.scrollY > 500);
        });

        // Scroll Reveal
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('show');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });

        document.querySelectorAll('.fluent-reveal, .fluent-stat-card, .fluent-card, .fluent-glass-dark, .fluent-glass').forEach(el => {
            observer.observe(el);
        });

        // Animate stat bar widths after paint
        requestAnimationFrame(() => {
            document.querySelectorAll('[data-width]').forEach(bar => {
                bar.style.width = bar.getAttribute('data-width');
            });
        });

        // Chart
        const chartElement = document.getElementById('landingChart');
        if (chartElement) {
            const ctx = chartElement.getContext('2d');
            const usageData = @json($usagePerLab);
            const chartLabels = usageData.map(item => item.label);
            const chartValues = usageData.map(item => Number(item.count));
            const usageTotal = chartValues.reduce((sum, v) => sum + v, 0);

            const centerTextPlugin = {
                id: 'centerTextPlugin',
                afterDraw(chart) {
                    const meta = chart.getDatasetMeta(0);
                    if (!meta?.data?.length) return;
                    const { x, y } = meta.data[0];
                    const { ctx } = chart;
                    ctx.save();
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillStyle = 'rgba(255,255,255,0.6)';
                    ctx.font = '600 10px "Segoe UI Variable","Segoe UI",Roboto,sans-serif';
                    ctx.fillText('TOTAL', x, y - 12);
                    ctx.fillStyle = '#fff';
                    ctx.font = '700 21px "Segoe UI Variable","Segoe UI",Roboto,sans-serif';
                    ctx.fillText(String(usageTotal), x, y + 8);
                    ctx.restore();
                }
            };

            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        data: chartValues,
                        backgroundColor: ['rgba(56,189,248,0.82)', 'rgba(34,211,238,0.78)', 'rgba(255,255,255,0.40)', 'rgba(255,255,255,0.25)'],
                        borderColor: ['rgba(56,189,248,1)', 'rgba(34,211,238,1)', 'rgba(255,255,255,0.7)', 'rgba(255,255,255,0.5)'],
                        borderWidth: 1.5,
                        borderRadius: 5,
                        hoverOffset: 10,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '58%',
                    layout: { padding: 2 },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label(ctx) {
                                    const v = Number(ctx.raw || 0);
                                    const pct = usageTotal > 0 ? ((v / usageTotal) * 100).toFixed(1) : '0.0';
                                    return `${ctx.label}: ${v} aktivitas (${pct}%)`;
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