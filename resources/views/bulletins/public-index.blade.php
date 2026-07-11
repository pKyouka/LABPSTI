<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kabar Berita — {{ config('app.name', 'LABPSTI') }}</title>
    @include('partials.favicon')
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=roboto:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-[#F3F3F3] text-[#1F1F1F]">
    <div aria-hidden="true" class="fixed inset-0 -z-10 fluent-mica"></div>
    <div aria-hidden="true" class="fixed top-0 inset-x-0 h-0.5 bg-accent z-50"></div>

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
                <a href="{{ route('landing') }}#bulletins" class="fluent-btn fluent-btn-ghost fluent-btn-sm !rounded-lg">← Beranda</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="fluent-btn fluent-btn-primary fluent-btn-sm !px-4">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="fluent-btn fluent-btn-sm !px-4 font-medium text-neutral-700 bg-black/[0.06] border border-neutral-200/70 hover:bg-black/[0.09] rounded-lg transition">Login</a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="py-14">
        <section class="max-w-7xl mx-auto px-5 sm:px-6 lg:px-8">
            <div class="mb-12">
                <div class="fluent-section-pill mb-4">Informasi</div>
                <h1 class="fluent-section-heading">Kabar Berita</h1>
                <p class="fluent-section-sub mt-3">Rangkuman kegiatan terbaru laboratorium, praktik lapangan mahasiswa, dan perkembangan riset di Data Engineering serta Digital Forensik.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($bulletins as $bulletin)
                    <article class="fluent-glow-border group flex flex-col fluent-card overflow-hidden">
                        <a href="{{ route('landing.bulletins.show', $bulletin) }}" class="block overflow-hidden relative">
                            <img src="{{ $bulletin->image_url }}" alt="{{ $bulletin->title }}" class="h-52 w-full object-cover group-hover:scale-105 transition duration-600">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/30 via-transparent to-transparent"></div>
                            <div class="absolute top-4 left-4">
                                <span class="fluent-badge fluent-badge-info shadow-sm backdrop-blur-sm bg-accent-light/90">{{ $bulletin->category->name }}</span>
                            </div>
                        </a>
                        <div class="p-5 flex flex-col flex-1">
                            <div class="flex items-center justify-between text-[11px] mb-3">
                                <span class="fluent-badge fluent-badge-neutral text-[10px]">{{ $bulletin->category->name }}</span>
                                <span class="font-medium text-neutral-400">{{ $bulletin->published_at?->format('d M Y') }}</span>
                            </div>
                            <h2 class="text-base font-semibold text-neutral-800 leading-snug line-clamp-2 mb-2.5">
                                <a href="{{ route('landing.bulletins.show', $bulletin) }}" class="hover:text-accent transition">{{ $bulletin->title }}</a>
                            </h2>
                            <p class="text-[13px] text-neutral-500 leading-relaxed line-clamp-3 mb-5">{{ strip_tags($bulletin->content) }}</p>
                            <a href="{{ route('landing.bulletins.show', $bulletin) }}" class="mt-auto inline-flex items-center gap-1.5 text-[13px] font-semibold text-accent hover:text-accent-hover transition group/btn">
                                Lihat Detail
                                <svg class="w-3.5 h-3.5 group-hover/btn:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </article>
                @empty
                    <div class="col-span-3 text-center py-24 bg-white rounded-2xl border-2 border-dashed border-neutral-200">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-neutral-100 grid place-items-center">
                            <svg class="w-7 h-7 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                        </div>
                        <p class="text-neutral-400 font-medium text-base">Belum ada kabar berita yang dipublikasikan.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-10">{{ $bulletins->links() }}</div>
        </section>
    </main>

    <footer class="relative bg-neutral-950 text-white py-14 mt-12">
        <div class="max-w-7xl mx-auto px-5 sm:px-6 lg:px-8 text-center">
            <div class="flex items-center justify-center gap-3 mb-4">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-accent to-accent-700 grid place-items-center">
                    <span class="text-[10px] font-bold tracking-tight">TI</span>
                </div>
                <span class="text-base font-semibold tracking-tight">LAB<span class="text-accent">PSTI</span></span>
            </div>
            <p class="text-[11px] font-medium text-neutral-500 tracking-[0.12em] uppercase">&copy; {{ date('Y') }} PSTI UNISA Yogyakarta. "Health Information Technology, Empowering Healthier Life."</p>
        </div>
    </footer>
</body>
</html>