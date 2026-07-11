<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $category->name }} — {{ config('app.name', 'LABPSTI') }}</title>
    @include('partials.favicon')
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=roboto:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-[#F3F3F3] text-[#1F1F1F]">
    <div aria-hidden="true" class="fixed inset-0 -z-10 fluent-mica"></div>
    <div aria-hidden="true" class="fixed top-0 inset-x-0 h-0.5 bg-accent z-50"></div>

    <!-- Navbar -->
    <nav class="sticky top-0 z-40 fluent-topbar border-b border-black/[0.06]" x-data="{ labsOpen: false }">
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
            <div class="hidden md:flex items-center gap-0.5">
                <div class="relative" @mouseenter="labsOpen = true" @mouseleave="labsOpen = false">
                    <button class="fl-nav-link flex items-center gap-1 px-3 py-2 text-[13px] font-medium text-neutral-600 hover:text-neutral-900 rounded-lg transition">
                        Laboratorium
                        <svg class="w-3 h-3 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="m6 9 6 6 6-6"/></svg>
                    </button>
                    <div x-show="labsOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100" class="absolute top-full left-0 mt-1.5 w-56 fluent-glass rounded-xl overflow-hidden z-50" style="display:none">
                        @foreach($categories as $cat)
                            <a href="{{ route('lab.show', $cat->id) }}" class="block px-4 py-3 text-[13px] font-medium transition first:rounded-t-xl last:rounded-b-xl {{ $cat->id == $category->id ? 'bg-accent-light text-accent font-semibold' : 'text-neutral-600 hover:bg-accent-light hover:text-accent' }}">
                                {{ $cat->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
                <a href="{{ route('landing') }}#bulletins" class="fl-nav-link px-3 py-2 text-[13px] font-medium text-neutral-600 hover:text-neutral-900 rounded-lg transition">Kabar Berita</a>
                <a href="{{ route('landing') }}#stats" class="fl-nav-link px-3 py-2 text-[13px] font-medium text-neutral-600 hover:text-neutral-900 rounded-lg transition">Statistik</a>
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

    <!-- Lab Hero -->
    <section class="relative overflow-hidden bg-gradient-to-br from-accent-950 via-accent-900 to-accent-800 text-white py-20">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_1px_1px,rgba(255,255,255,0.04)_1px,transparent_0)] [background-size:10px_10px] opacity-25"></div>
        <div class="fluent-glow fluent-glow-accent w-[500px] h-[500px] -top-32 -right-32" style="background:rgba(56,189,248,0.12)"></div>
        <div class="fluent-glow fluent-glow-purple w-[350px] h-[350px] -bottom-24 -left-20" style="background:rgba(139,92,246,0.07)"></div>

        <div class="relative max-w-7xl mx-auto px-5 sm:px-6 lg:px-8 text-center">
            <div class="mb-6 inline-flex p-4 rounded-2xl bg-white/10 backdrop-blur-md ring-1 ring-white/10">
                @if($category->name == 'Data Engineering')
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                @else
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                @endif
            </div>
            <h1 class="text-4xl sm:text-5xl font-semibold mb-4 tracking-tight">{{ $category->name }}</h1>
            <p class="text-base lg:text-lg text-accent-200/80 max-w-2xl mx-auto leading-relaxed">
                @if($category->name == 'Data Engineering')
                    Membangun masa depan melalui pengolahan data cerdas dan arsitektur sistem informasi kesehatan digital yang inovatif.
                @else
                    Menjamin integritas dan keamanan data digital melalui investigasi forensik profesional berbasis nilai-nilai Islam.
                @endif
            </p>
        </div>
    </section>

    <!-- Content -->
    <main class="py-16">
        <div class="max-w-7xl mx-auto px-5 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 space-y-14">
                    <!-- Profile -->
                    <section>
                        <h2 class="text-[1.75rem] font-semibold text-neutral-800 mb-6 flex items-center gap-3">
                            <span class="w-9 h-9 rounded-xl bg-accent-light text-accent grid place-items-center text-sm font-bold">1</span>
                            Profil Laboratorium
                        </h2>
                        <div class="prose prose-neutral max-w-none text-[15px] text-neutral-600 leading-relaxed space-y-4">
                            @if($category->name == 'Data Engineering')
                                <p>Laboratorium Data Engineering PSTI UNISA Yogyakarta menyelenggarakan disiplin ilmu teknologi informasi yang berintegrasi pada bidang ilmu kesehatan melalui mata kuliah unggulan diantaranya Teknologi Informasi Rumah Sakit dan Inovasi Kesehatan Digital.</p>
                                <p>Fokus utama kami adalah melakukan perencanaan ide terhadap inovasi teknologi yang bercirikan KESEHATAN dan basis NILAI-NILAI ISLAM BERKEMAJUAN.</p>
                            @else
                                <p>Laboratorium Digital Forensik berfokus pada penerapan disiplin Ilmu Teknologi Informasi dan tren teknologi pada masa mendatang untuk investigasi dan keamanan data.</p>
                                <p>Kami menerapkan prinsip Akhlak Mulia, Amanah, Profesional, Kolaboratif, dan Unggul dalam setiap proses investigasi bukti digital untuk mendukung ekosistem kesehatan digital yang aman.</p>
                            @endif
                        </div>
                    </section>

                    <!-- Bulletins -->
                    <section>
                        <h2 class="text-[1.75rem] font-semibold text-neutral-800 mb-6 flex items-center gap-3">
                            <span class="w-9 h-9 rounded-xl bg-accent-light text-accent grid place-items-center text-sm font-bold">2</span>
                            Berita Terbaru {{ $category->name }}
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            @forelse($bulletins as $bulletin)
                                <div class="fluent-card p-6 group">
                                    <p class="text-[11px] font-semibold text-accent mb-3 uppercase tracking-wider">{{ $bulletin->published_at?->format('d M Y') }}</p>
                                    <h3 class="text-lg font-semibold text-neutral-800 mb-3 line-clamp-2 group-hover:text-accent transition-colors leading-snug">
                                        <a href="{{ route('lab.bulletins.show', ['category' => $category, 'bulletin' => $bulletin]) }}">{{ $bulletin->title }}</a>
                                    </h3>
                                    <p class="text-[13px] text-neutral-500 mb-5 line-clamp-3 leading-relaxed">{!! strip_tags($bulletin->content) !!}</p>
                                    <a href="{{ route('lab.bulletins.show', ['category' => $category, 'bulletin' => $bulletin]) }}" class="inline-flex items-center gap-1.5 text-[13px] font-semibold text-accent hover:text-accent-hover transition group/btn">
                                        Baca Selengkapnya
                                        <svg class="w-3.5 h-3.5 group-hover/btn:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                </div>
                            @empty
                                <div class="col-span-2 py-16 text-center bg-white rounded-2xl border-2 border-dashed border-neutral-200">
                                    <p class="text-neutral-400 font-medium">Belum ada berita khusus untuk lab ini.</p>
                                </div>
                            @endforelse
                        </div>
                    </section>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <div class="fluent-card p-6">
                        <h3 class="text-lg font-semibold text-neutral-800 mb-6 flex items-center gap-2">
                            <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                            Statistik Lab
                        </h3>
                        <div class="space-y-6">
                            <div class="flex items-center gap-4">
                                <div class="w-11 h-11 rounded-xl bg-accent-light flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-neutral-800">{{ $usageCount }}</p>
                                    <p class="text-[11px] font-medium text-neutral-400 uppercase tracking-wider">Total Penggunaan</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="w-11 h-11 rounded-xl bg-accent-light flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-neutral-800">{{ count($bulletins) }}</p>
                                    <p class="text-[11px] font-medium text-neutral-400 uppercase tracking-wider">Berita Terkait</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-accent to-accent-700 rounded-2xl shadow-fluent-lg p-6 text-white overflow-hidden relative">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-white/5 rounded-full -mr-6 -mt-6 blur-2xl"></div>
                        <h3 class="text-lg font-semibold mb-3 relative">Aktivitas Lab</h3>
                        <p class="text-[13px] text-accent-100 mb-6 leading-relaxed relative">Lakukan pencatatan aktivitas penggunaan laboratorium secara tertib.</p>
                        <a href="{{ route('login') }}" class="relative fluent-btn w-full justify-center bg-white text-accent font-semibold rounded-xl hover:bg-accent-light transition shadow-lg">
                            Catat Penggunaan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="relative bg-neutral-950 text-white pt-16 pb-10 overflow-hidden">
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top,rgba(0,120,212,0.06)_0%,transparent_60%)]"></div>
        <div class="relative max-w-7xl mx-auto px-5 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12">
                <div class="md:col-span-2">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-accent to-accent-700 grid place-items-center shadow-lg shadow-accent/20">
                            <span class="text-sm font-bold tracking-tight">TI</span>
                        </div>
                        <span class="text-xl font-semibold tracking-tight">LAB<span class="text-accent">PSTI</span></span>
                    </div>
                    <p class="text-sm text-neutral-400 leading-relaxed max-w-md">Program Studi Teknologi Informasi (PSTI) Universitas 'Aisyiyah (UNISA) Yogyakarta menerapkan disiplin Ilmu Teknologi Informasi yang berintegrasi pada bidang ilmu kesehatan.</p>
                </div>
                <div>
                    <h3 class="text-[11px] font-semibold text-accent mb-5 uppercase tracking-[0.15em]">Navigasi</h3>
                    <ul class="space-y-3 text-[13px] font-medium text-neutral-400">
                        <li><a href="{{ route('landing') }}" class="hover:text-white transition-colors">Halaman Utama</a></li>
                        <li><a href="{{ route('landing') }}#bulletins" class="hover:text-white transition-colors">Kabar Berita</a></li>
                        <li><a href="{{ route('landing') }}#stats" class="hover:text-white transition-colors">Statistik</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-white transition-colors">Admin Panel</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-[11px] font-semibold text-accent mb-5 uppercase tracking-[0.15em]">Hubungi Kami</h3>
                    <ul class="space-y-4 text-[13px] font-medium text-neutral-400">
                        <li class="flex items-start gap-3"><svg class="w-4 h-4 text-neutral-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg><span>Jl. Siliwangi (Ring Road Barat) No. 63, Sleman, Yogyakarta.</span></li>
                        <li class="flex items-center gap-3"><svg class="w-4 h-4 text-neutral-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg><span>info@unisayogya.ac.id</span></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-neutral-800 mt-12 pt-7 text-center">
                <p class="text-[11px] font-medium text-neutral-500 tracking-[0.12em] uppercase">&copy; {{ date('Y') }} PSTI UNISA Yogyakarta. "Health Information Technology, Empowering Healthier Life."</p>
            </div>
        </div>
    </footer>
</body>
</html>