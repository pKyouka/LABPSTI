<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $bulletin->title }} — Kabar Berita</title>
    @include('partials.favicon')
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=roboto:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-[#F3F3F3] text-[#1F1F1F]">
    <div aria-hidden="true" class="fixed inset-0 -z-10 fluent-mica"></div>
    <div aria-hidden="true" class="fixed top-0 inset-x-0 h-0.5 bg-accent z-50"></div>

    <nav class="sticky top-0 z-40 fluent-topbar border-b border-black/[0.06]">
        <div class="max-w-5xl mx-auto px-5 sm:px-6 lg:px-8 flex justify-between h-[54px] items-center">
            <a href="{{ route('landing') }}" class="flex items-center gap-2.5 flex-shrink-0 group">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-accent to-accent-800 text-white grid place-items-center flex-shrink-0 shadow-fluent-sm group-hover:shadow-fluent-md group-hover:scale-105 transition-all duration-200">
                    <span class="text-xs font-bold tracking-tight">TI</span>
                </div>
                <span class="text-sm font-semibold text-neutral-800 tracking-tight">LAB<span class="text-accent">PSTI</span></span>
            </a>
            <div class="flex items-center gap-2">
                <a href="{{ route('landing.bulletins') }}" class="fluent-btn fluent-btn-ghost fluent-btn-sm !rounded-lg">Semua Berita</a>
                <a href="{{ route('landing') }}" class="fluent-btn fluent-btn-ghost fluent-btn-sm !rounded-lg">Beranda</a>
            </div>
        </div>
    </nav>

    <main class="py-10">
        <article class="max-w-5xl mx-auto px-5 sm:px-6 lg:px-8">
            <header class="mb-8">
                <div class="flex flex-wrap items-center gap-3 mb-4">
                    <span class="fluent-badge fluent-badge-info">{{ $bulletin->category->name }}</span>
                    <span class="text-sm font-medium text-neutral-500">{{ $bulletin->published_at?->format('d F Y, H:i') }}</span>
                </div>
                <h1 class="text-3xl sm:text-[2.5rem] font-semibold text-neutral-900 leading-tight tracking-tight">{{ $bulletin->title }}</h1>
                <p class="mt-4 text-[15px] text-neutral-500 max-w-3xl leading-relaxed">Berikut detail informasi kegiatan laboratorium yang telah dipublikasikan oleh PSTI UNISA Yogyakarta.</p>
            </header>

            <figure class="mb-8 overflow-hidden rounded-2xl border border-neutral-200 bg-white shadow-fluent-sm">
                <img src="{{ $bulletin->image_url }}" alt="{{ $bulletin->title }}" class="w-full h-auto object-cover">
            </figure>

            <section class="fluent-card p-6 sm:p-10">
                <div class="prose prose-neutral max-w-none prose-headings:font-semibold prose-p:leading-relaxed prose-a:text-accent prose-img:rounded-xl">
                    {!! nl2br(e($bulletin->content)) !!}
                </div>
            </section>
        </article>

        @if($relatedBulletins->isNotEmpty())
            <section class="max-w-5xl mx-auto px-5 sm:px-6 lg:px-8 mt-14">
                <h2 class="text-2xl font-semibold text-neutral-800 mb-6">Berita Terkait</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach($relatedBulletins as $related)
                        <article class="fluent-card overflow-hidden group">
                            <a href="{{ route('landing.bulletins.show', $related) }}" class="block overflow-hidden">
                                <img src="{{ $related->image_url }}" alt="{{ $related->title }}" class="h-36 w-full object-cover group-hover:scale-105 transition duration-500">
                            </a>
                            <div class="p-4">
                                <p class="text-[11px] font-medium text-neutral-400 uppercase tracking-wider">{{ $related->published_at?->format('d M Y') }}</p>
                                <h3 class="mt-2 text-[13px] font-semibold text-neutral-800 leading-snug line-clamp-2">
                                    <a href="{{ route('landing.bulletins.show', $related) }}" class="hover:text-accent transition">{{ $related->title }}</a>
                                </h3>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif
    </main>

    <footer class="relative bg-neutral-950 text-white py-12 mt-12">
        <div class="max-w-7xl mx-auto px-5 sm:px-6 lg:px-8 text-center">
            <div class="flex items-center justify-center gap-3 mb-3">
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