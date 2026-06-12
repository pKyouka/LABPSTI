<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $bulletin->title }} - Kabar Berita</title>
    @include('partials.favicon')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-slate-50 text-slate-900">
    <nav class="bg-white border-b border-slate-200 sticky top-0 z-40">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="{{ route('landing') }}" class="text-lg font-black text-blue-900 tracking-tight">LAB<span class="text-blue-600">PSTI</span></a>
            <div class="flex items-center gap-4">
                <a href="{{ route('landing.bulletins') }}" class="text-sm font-bold text-slate-600 hover:text-blue-600">Semua Berita</a>
                <a href="{{ route('landing') }}" class="text-sm font-bold text-slate-600 hover:text-blue-600">Beranda</a>
            </div>
        </div>
    </nav>

    <main class="py-10">
        <article class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <header class="mb-8">
                <div class="flex flex-wrap items-center gap-3 text-xs mb-4">
                    <span class="inline-flex px-3 py-1 rounded-full bg-blue-100 text-blue-700 font-bold uppercase tracking-wide">{{ $bulletin->category->name }}</span>
                    <span class="text-slate-500 font-semibold">{{ $bulletin->published_at?->format('d F Y, H:i') }}</span>
                </div>

                <h1 class="text-4xl sm:text-5xl font-black text-slate-900 leading-tight">{{ $bulletin->title }}</h1>
                <p class="mt-4 text-slate-600 max-w-3xl">Berikut detail informasi kegiatan laboratorium yang telah dipublikasikan oleh PSTI UNISA Yogyakarta.</p>
            </header>

            <figure class="mb-10 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <img src="{{ $bulletin->image_url }}" alt="{{ $bulletin->title }}" class="w-full h-auto object-cover">
            </figure>

            <section class="bg-white border border-slate-100 rounded-3xl p-6 sm:p-10 shadow-sm">
                <div class="prose prose-slate max-w-none prose-headings:font-black prose-a:text-blue-700 prose-img:rounded-2xl">
                    {!! $bulletin->content !!}
                </div>
            </section>
        </article>

        @if($relatedBulletins->isNotEmpty())
            <section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 mt-12">
                <h2 class="text-2xl font-black text-slate-900 mb-6">Berita Terkait</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($relatedBulletins as $related)
                        <article class="bg-white rounded-2xl border border-slate-100 overflow-hidden hover:shadow-lg transition">
                            <a href="{{ route('landing.bulletins.show', $related) }}" class="block overflow-hidden">
                                <img src="{{ $related->image_url }}" alt="{{ $related->title }}" class="h-36 w-full object-cover hover:scale-105 transition duration-500">
                            </a>
                            <div class="p-4">
                                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wide">{{ $related->published_at?->format('d M Y') }}</p>
                                <h3 class="mt-2 text-sm font-black text-slate-800 leading-snug line-clamp-2">
                                    <a href="{{ route('landing.bulletins.show', $related) }}" class="hover:text-blue-600 transition">{{ $related->title }}</a>
                                </h3>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif
    </main>
</body>
</html>
