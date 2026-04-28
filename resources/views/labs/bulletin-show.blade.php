<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $bulletin->title }} - {{ $category->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-slate-50 text-slate-900">
    <nav class="bg-white/90 backdrop-blur-md border-b border-blue-100 sticky top-0 z-40">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="{{ route('landing') }}" class="text-lg font-black text-blue-900 tracking-tight">LAB<span class="text-blue-600">PSTI</span></a>
            <div class="flex items-center gap-4 text-sm font-bold">
                <a href="{{ route('lab.show', $category) }}" class="text-slate-600 hover:text-blue-600">Kembali ke {{ $category->name }}</a>
                <a href="{{ route('landing.bulletins') }}" class="text-slate-600 hover:text-blue-600">Semua Berita</a>
            </div>
        </div>
    </nav>

    <main class="py-12">
        <article class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <header class="mb-8">
                <div class="flex flex-wrap items-center gap-3 mb-4 text-xs">
                    <span class="inline-flex px-3 py-1 rounded-full bg-blue-100 text-blue-700 font-black uppercase tracking-wide">{{ $category->name }}</span>
                    <span class="text-slate-500 font-semibold">{{ $bulletin->published_at?->translatedFormat('d F Y, H:i') }}</span>
                </div>

                <h1 class="text-4xl md:text-5xl font-black tracking-tight leading-tight text-slate-900">{{ $bulletin->title }}</h1>
                <p class="mt-4 text-slate-600 max-w-4xl leading-relaxed">Berita ini merangkum aktivitas terbaru laboratorium secara lebih lengkap, termasuk konteks kegiatan, capaian mahasiswa, dan tindak lanjut yang disepakati pada sesi pendampingan.</p>
            </header>

            <figure class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm mb-10">
                <img src="{{ $bulletin->image_url }}" alt="{{ $bulletin->title }}" class="w-full h-auto object-cover">
            </figure>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <section class="lg:col-span-8 bg-white border border-slate-100 rounded-3xl p-6 sm:p-10 shadow-sm">
                    <div class="prose prose-slate max-w-none prose-headings:font-black prose-p:leading-relaxed">
                        {!! $bulletin->content !!}
                    </div>
                </section>

                <aside class="lg:col-span-4 space-y-6">
                    <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm">
                        <h2 class="text-lg font-black text-slate-900 mb-3">Ringkasan</h2>
                        <p class="text-sm text-slate-600 leading-relaxed">Dokumentasi ini disusun untuk membantu mahasiswa melihat alur praktik secara utuh: mulai dari persiapan, eksekusi teknis, evaluasi hasil, hingga rencana perbaikan pada sesi berikutnya.</p>
                    </div>

                    <div class="bg-blue-600 text-white rounded-3xl p-6 shadow-lg">
                        <h2 class="text-lg font-black mb-2">Perlu Catat Aktivitas?</h2>
                        <p class="text-sm text-blue-100 mb-5">Gunakan sistem pencatatan lab agar proses penggunaan fasilitas tetap terstruktur.</p>
                        <a href="{{ route('login') }}" class="block w-full text-center py-3 rounded-2xl bg-white text-blue-600 font-black hover:bg-blue-50 transition">CATAT PENGGUNAAN</a>
                    </div>
                </aside>
            </div>
        </article>

        @if($relatedBulletins->isNotEmpty())
            <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 mt-12">
                <h2 class="text-2xl font-black text-slate-900 mb-6">Berita Lain Di {{ $category->name }}</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($relatedBulletins as $related)
                        <article class="bg-white rounded-2xl border border-slate-100 overflow-hidden hover:shadow-lg transition">
                            <a href="{{ route('lab.bulletins.show', ['category' => $category, 'bulletin' => $related]) }}" class="block overflow-hidden">
                                <img src="{{ $related->image_url }}" alt="{{ $related->title }}" class="h-40 w-full object-cover hover:scale-105 transition duration-500">
                            </a>
                            <div class="p-4">
                                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wide">{{ $related->published_at?->format('d M Y') }}</p>
                                <h3 class="mt-2 text-sm font-black text-slate-800 leading-snug line-clamp-2">
                                    <a href="{{ route('lab.bulletins.show', ['category' => $category, 'bulletin' => $related]) }}" class="hover:text-blue-600 transition">{{ $related->title }}</a>
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
