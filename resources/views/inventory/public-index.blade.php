<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>List Inventaris — {{ config('app.name', 'LABPSTI') }}</title>
    @include('partials.favicon')
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=roboto:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-[#F3F3F3] text-[#1F1F1F]">
    <div aria-hidden="true" class="fixed inset-0 -z-10 fluent-mica"></div>
    <div aria-hidden="true" class="fixed top-0 inset-x-0 h-0.5 bg-accent z-50"></div>

    <!-- Navbar -->
    <nav class="sticky top-0 z-40 fluent-topbar border-b border-black/[0.06]">
        <div class="max-w-7xl mx-auto px-5 sm:px-6 lg:px-8 flex justify-between h-[54px] items-center">
            <a href="{{ route('landing') }}" class="flex items-center gap-2.5 flex-shrink-0 group">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-accent to-accent-800 text-white grid place-items-center flex-shrink-0 shadow-fluent-sm group-hover:shadow-fluent-md group-hover:scale-105 transition-all duration-200">
                    <span class="text-xs font-bold tracking-tight">TI</span>
                </div>
                <div class="hidden sm:block">
                    <span class="text-sm font-semibold text-neutral-800 tracking-tight">LAB<span class="text-accent">PSTI</span></span>
                    <span class="text-[9px] font-semibold text-neutral-400 tracking-[0.12em] uppercase block leading-none">UNISA YOGYAKARTA</span>
                </div>
            </a>
            <div class="flex items-center gap-2.5">
                <a href="{{ route('landing') }}" class="fluent-btn fluent-btn-ghost fluent-btn-sm !rounded-lg">← Beranda</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="fluent-btn fluent-btn-primary fluent-btn-sm !px-4">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="fluent-btn fluent-btn-sm !px-4 font-medium text-neutral-700 bg-black/[0.06] border border-neutral-200/70 hover:bg-black/[0.09] rounded-lg transition">Login</a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="py-14">
        <div class="max-w-7xl mx-auto px-5 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <div class="fluent-section-pill mb-4">Perlengkapan</div>
                <h1 class="fluent-section-heading">List Inventaris Barang</h1>
                <p class="fluent-section-sub mt-3 mx-auto text-center">Semua barang yang tersedia untuk dipinjam dapat dilihat tanpa login.</p>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-10">
                <div class="fluent-stat-card text-center">
                    <div class="w-10 h-10 mx-auto mb-3 rounded-xl bg-accent-light flex items-center justify-center">
                        <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </div>
                    <p class="text-3xl font-bold text-accent">{{ $summary['items_count'] }}</p>
                    <p class="text-[11px] font-medium text-neutral-400 mt-1">Item Tersedia</p>
                </div>
                <div class="fluent-stat-card !bg-gradient-to-br !from-emerald-500 !to-emerald-600 !text-white !border-emerald-400/30 text-center">
                    <p class="text-3xl font-bold">{{ $summary['available_stock_total'] }}</p>
                    <p class="text-[11px] font-medium text-emerald-100 mt-1">Stok Siap Pinjam</p>
                </div>
                <div class="fluent-stat-card text-center">
                    <p class="text-3xl font-bold text-neutral-800">{{ $summary['overall_stock_total'] }}</p>
                    <p class="text-[11px] font-medium text-neutral-400 mt-1">Total Seluruh Stok</p>
                </div>
                <div class="fluent-stat-card !bg-gradient-to-br !from-accent-700 !to-accent !text-white !border-accent-400/30 text-center">
                    <p class="text-3xl font-bold">{{ $summary['categories_count'] }}</p>
                    <p class="text-[11px] font-medium text-accent-100 mt-1">Kategori Barang</p>
                </div>
            </div>

            @if($inventoryItems->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                    @foreach($inventoryItems as $item)
                        <article class="fluent-card p-6 group">
                            <div class="flex items-start justify-between gap-3 mb-3">
                                <h2 class="text-lg font-semibold text-neutral-800 leading-tight">{{ $item->name }}</h2>
                                <span class="shrink-0 text-[11px] font-medium text-neutral-500 bg-neutral-100 rounded-md px-2.5 py-1">{{ $item->unit }}</span>
                            </div>
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-accent mb-4">{{ $item->category ?? 'Tanpa Kategori' }}</p>
                            <p class="text-[13px] text-neutral-500 leading-relaxed line-clamp-3 mb-5">
                                {{ $item->description ?: 'Tidak ada deskripsi barang.' }}
                            </p>
                            <!-- Stock bar -->
                            <div class="rounded-xl bg-surface border border-neutral-200 p-3 mb-4">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-[11px] font-medium text-neutral-400">Stok tersedia</span>
                                    <span class="text-[13px] font-semibold text-accent tabular-nums">{{ $item->available_stock }} / {{ $item->total_stock }}</span>
                                </div>
                                <div class="h-2 rounded-full bg-neutral-200 overflow-hidden">
                                    @php $stockPercent = $item->total_stock > 0 ? min(100, round(($item->available_stock / $item->total_stock) * 100)) : 0; @endphp
                                    <div class="h-full rounded-full bg-accent transition-all duration-700" data-width="{{ $stockPercent }}%" style="width:1%"></div>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3 text-[11px]">
                                <div class="rounded-lg bg-surface border border-neutral-200 p-2.5">
                                    <p class="font-medium text-neutral-400">Kondisi</p>
                                    <p class="font-semibold text-neutral-700 mt-0.5">{{ $item->condition ?: '-' }}</p>
                                </div>
                                <div class="rounded-lg bg-surface border border-neutral-200 p-2.5">
                                    <p class="font-medium text-neutral-400">Lokasi</p>
                                    <p class="font-semibold text-neutral-700 mt-0.5">{{ $item->location ?: '-' }}</p>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
                <div class="mt-10">{{ $inventoryItems->links() }}</div>
            @else
                <div class="text-center py-24 bg-white rounded-2xl border-2 border-dashed border-neutral-200">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-neutral-100 grid place-items-center">
                        <svg class="w-7 h-7 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </div>
                    <p class="text-neutral-400 font-medium text-base">Tidak ada barang yang tersedia untuk dipinjam saat ini.</p>
                </div>
            @endif
        </div>
    </main>

    <footer class="relative bg-neutral-950 text-white py-14 mt-10">
        <div class="max-w-7xl mx-auto px-5 sm:px-6 lg:px-8 text-center">
            <div class="flex items-center justify-center gap-3 mb-4">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-accent to-accent-700 grid place-items-center">
                    <span class="text-[10px] font-bold tracking-tight">TI</span>
                </div>
                <span class="text-base font-semibold tracking-tight">LAB<span class="text-accent">PSTI</span></span>
            </div>
            <p class="text-[11px] font-medium text-neutral-500 tracking-[0.12em] uppercase">&copy; {{ date('Y') }} PSTI UNISA Yogyakarta.</p>
        </div>
    </footer>

    <script>
        requestAnimationFrame(() => {
            document.querySelectorAll('[data-width]').forEach(bar => {
                bar.style.width = bar.getAttribute('data-width');
            });
        });
    </script>
</body>
</html>