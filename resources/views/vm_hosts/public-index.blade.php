<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Status Host VM — {{ config('app.name', 'LABPSTI') }}</title>
    @include('partials.favicon')
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=roboto:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-[#F3F3F3] text-[#1F1F1F]">
    <!-- Mica Background -->
    <div aria-hidden="true" class="fixed inset-0 -z-10 fluent-mica"></div>

    <!-- Accent Top Bar -->
    <div aria-hidden="true" class="fixed top-0 inset-x-0 h-0.5 bg-accent z-50"></div>

    <!-- Navbar -->
    <nav class="sticky top-0 z-40 fluent-topbar border-b border-black/[0.06]">
        <div class="max-w-7xl mx-auto px-5 sm:px-6 lg:px-8 flex justify-between h-[54px] items-center">
            <a href="{{ route('landing') }}" class="flex items-center gap-2.5 flex-shrink-0 group">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-accent to-accent-800 text-white grid place-items-center flex-shrink-0 shadow-fluent-sm">
                    <span class="text-xs font-bold tracking-tight">TI</span>
                </div>
                <div class="hidden sm:block">
                    <span class="text-sm font-semibold text-neutral-800 tracking-tight">LAB<span class="text-accent">PSTI</span></span>
                    <span class="text-[9px] font-semibold text-neutral-400 tracking-[0.12em] uppercase block leading-none">UNISA YOGYAKARTA</span>
                </div>
            </a>
            <div class="flex items-center gap-2.5">
                <a href="{{ route('landing') }}" class="fluent-btn fluent-btn-sm !px-4 font-medium text-neutral-700 bg-black/[0.04] border border-neutral-200/60 hover:bg-black/[0.06] rounded-lg transition">Beranda</a>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <section class="py-12 lg:py-16">
        <div class="max-w-7xl mx-auto px-5 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <div class="fluent-section-pill mb-4">Infrastruktur</div>
                <h1 class="text-[2rem] sm:text-[2.5rem] font-semibold text-neutral-900 tracking-tight">Status Host Virtual Machine</h1>
                <p class="mt-3 text-base text-neutral-500 max-w-2xl mx-auto">Pantau ketersediaan resource host VM yang digunakan untuk peminjaman Virtual Machine laboratorium.</p>
            </div>

            @if($hosts->isEmpty())
                <div class="text-center py-20 bg-white rounded-2xl border-2 border-dashed border-neutral-200">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-neutral-100 grid place-items-center">
                        <svg class="w-7 h-7 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <p class="text-neutral-400 font-medium text-base">Belum ada data host VM yang tersedia.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($hosts as $host)
                        @php
                            $cpuPct = $host->cpuUsagePercent();
                            $ramPct = $host->ramUsagePercent();
                            $storagePct = $host->storageUsagePercent();
                            $isFull = $host->isFull();
                            $isActive = $host->is_active;
                        @endphp
                        <div class="bg-white rounded-2xl border border-neutral-200/60 p-6 hover:bg-neutral-50/50 hover:shadow-sm transition-colors {{ !$isActive ? 'opacity-50' : '' }}">
                            <!-- Header -->
                            <div class="flex items-start justify-between mb-5">
                                <div>
                                    <h3 class="text-lg font-semibold text-neutral-800">{{ $host->name }}</h3>
                                    @if($host->ip_address)
                                        <p class="text-[13px] text-neutral-400 font-mono mt-0.5">{{ $host->ip_address }}</p>
                                    @endif
                                </div>
                                <span class="fluent-badge {{ $host->statusBadgeClass() }}">
                                    {{ $host->statusLabel() }}
                                </span>
                            </div>

                            <!-- CPU Progress -->
                            <div class="mb-4">
                                <div class="flex justify-between text-[12px] font-medium mb-1.5">
                                    <span class="text-neutral-500">CPU</span>
                                    <span class="tabular-nums {{ $cpuPct >= 90 ? 'text-red-600 font-semibold' : 'text-neutral-600' }}">
                                        {{ $host->cpu_used }} / {{ $host->cpu_total }} Cores ({{ $cpuPct }}%)
                                    </span>
                                </div>
                                <div class="w-full h-2 rounded-full bg-neutral-100 overflow-hidden">
                                    <div class="h-full rounded-full {{ $cpuPct >= 90 ? 'bg-red-500' : 'bg-neutral-400' }}"
                                         style="width: {{ min($cpuPct, 100) }}%"></div>
                                </div>
                            </div>

                            <!-- RAM Progress -->
                            <div class="mb-4">
                                <div class="flex justify-between text-[12px] font-medium mb-1.5">
                                    <span class="text-neutral-500">RAM</span>
                                    <span class="tabular-nums {{ $ramPct >= 90 ? 'text-red-600 font-semibold' : 'text-neutral-600' }}">
                                        {{ number_format($host->ram_used / 1024, 1) }} / {{ number_format($host->ram_total / 1024, 1) }} GB ({{ $ramPct }}%)
                                    </span>
                                </div>
                                <div class="w-full h-2 rounded-full bg-neutral-100 overflow-hidden">
                                    <div class="h-full rounded-full {{ $ramPct >= 90 ? 'bg-red-500' : 'bg-neutral-400' }}"
                                         style="width: {{ min($ramPct, 100) }}%"></div>
                                </div>
                            </div>

                            <!-- Storage Progress -->
                            <div class="mb-5">
                                <div class="flex justify-between text-[12px] font-medium mb-1.5">
                                    <span class="text-neutral-500">Storage</span>
                                    <span class="tabular-nums {{ $storagePct >= 90 ? 'text-red-600 font-semibold' : 'text-neutral-600' }}">
                                        {{ $host->storage_used }} / {{ $host->storage_total }} GB ({{ $storagePct }}%)
                                    </span>
                                </div>
                                <div class="w-full h-2 rounded-full bg-neutral-100 overflow-hidden">
                                    <div class="h-full rounded-full {{ $storagePct >= 90 ? 'bg-red-500' : 'bg-neutral-400' }}"
                                         style="width: {{ min($storagePct, 100) }}%"></div>
                                </div>
                            </div>

                            <!-- Quick Info -->
                            <div class="grid grid-cols-3 gap-3 pt-4 border-t border-neutral-100">
                                <div class="text-center">
                                    <p class="text-lg font-bold tabular-nums {{ $host->cpuAvailable() > 0 ? 'text-neutral-700' : 'text-red-500' }}">{{ $host->cpuAvailable() }}</p>
                                    <p class="text-[10px] font-medium text-neutral-400 uppercase tracking-wider">CPU Free</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-lg font-bold tabular-nums {{ $host->ramAvailable() > 0 ? 'text-neutral-700' : 'text-red-500' }}">{{ number_format($host->ramAvailable() / 1024, 1) }}G</p>
                                    <p class="text-[10px] font-medium text-neutral-400 uppercase tracking-wider">RAM Free</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-lg font-bold tabular-nums {{ $host->storageAvailable() > 0 ? 'text-neutral-700' : 'text-red-500' }}">{{ $host->storageAvailable() }}G</p>
                                    <p class="text-[10px] font-medium text-neutral-400 uppercase tracking-wider">Storage Free</p>
                                </div>
                            </div>

                            @if(!$isActive)
                                <div class="mt-4 pt-4 border-t border-neutral-100">
                                    <p class="text-[12px] text-red-500 font-medium flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Host sedang nonaktif
                                    </p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Legend -->
                <div class="mt-10 flex flex-wrap justify-center gap-6 text-[12px] font-medium text-neutral-500">
                    <span class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-neutral-400 inline-block"></span> Digunakan
                    </span>
                    <span class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-neutral-100 border border-neutral-300 inline-block"></span> Tersedia
                    </span>
                    <span class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-red-500 inline-block"></span> Penuh
                    </span>
                </div>
            @endif
        </div>
    </section>

    <!-- Footer -->
    <footer class="relative bg-neutral-950 text-white py-12 overflow-hidden mt-8">
        <div class="relative max-w-7xl mx-auto px-5 sm:px-6 lg:px-8 text-center">
            <p class="text-[11px] font-medium text-neutral-500 tracking-[0.12em] uppercase">&copy; {{ date('Y') }} PSTI UNISA Yogyakarta.</p>
        </div>
    </footer>
</body>
</html>