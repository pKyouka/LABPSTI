<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kabar Berita - PSTI UNISA</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-slate-50 text-slate-900">
    <nav class="bg-white border-b border-slate-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="{{ route('landing') }}" class="text-lg font-black text-blue-900 tracking-tight">LAB<span class="text-blue-600">PSTI</span></a>
            <a href="{{ route('landing') }}#bulletins" class="text-sm font-bold text-slate-600 hover:text-blue-600">Kembali ke Beranda</a>
        </div>
    </nav>

    <main class="py-14">
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <h1 class="text-4xl font-black text-slate-900">Kabar Berita</h1>
                <p class="mt-3 text-slate-600 max-w-3xl">Rangkuman kegiatan terbaru laboratorium, praktik lapangan mahasiswa, dan perkembangan riset di Data Engineering serta Digital Forensik.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($bulletins as $bulletin)
                    <article class="group bg-white rounded-3xl overflow-hidden border border-slate-100 shadow-sm hover:shadow-xl transition">
                        <a href="{{ route('landing.bulletins.show', $bulletin) }}" class="block overflow-hidden">
                            <img src="{{ $bulletin->image_url }}" alt="{{ $bulletin->title }}" class="h-56 w-full object-cover group-hover:scale-105 transition duration-500">
                        </a>

                        <div class="p-6 flex flex-col">
                            <div class="flex items-center justify-between text-xs mb-3">
                                <span class="inline-flex px-3 py-1 rounded-full bg-blue-100 text-blue-700 font-bold uppercase tracking-wide">{{ $bulletin->category->name }}</span>
                                <span class="text-slate-400 font-semibold">{{ $bulletin->published_at?->format('d M Y') }}</span>
                            </div>

                            <h2 class="text-xl font-black text-slate-900 leading-snug line-clamp-2">
                                <a href="{{ route('landing.bulletins.show', $bulletin) }}" class="hover:text-blue-600 transition">{{ $bulletin->title }}</a>
                            </h2>

                            <p class="mt-4 text-sm text-slate-600 leading-relaxed line-clamp-3">{{ strip_tags($bulletin->content) }}</p>

                            <a href="{{ route('landing.bulletins.show', $bulletin) }}" class="mt-6 inline-flex items-center text-sm font-black text-blue-600 hover:text-blue-800 transition">
                                Lihat Detail
                                <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </a>
                        </div>
                    </article>
                @empty
                    <div class="col-span-3 text-center py-20 rounded-3xl border-2 border-dashed border-slate-200 bg-white">
                        <p class="text-slate-500 font-semibold">Belum ada kabar berita yang dipublikasikan.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-10">
                {{ $bulletins->links() }}
            </div>
        </section>
    </main>
</body>
</html>
