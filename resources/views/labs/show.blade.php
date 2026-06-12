<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $category->name }} - PSTI UNISA Yogyakarta</title>
    @include('partials.favicon')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-slate-50 text-slate-900">
    <!-- Top Bar -->
    <div class="bg-blue-900 text-white py-2 text-xs font-medium">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center">
            <div>
                :: C O R E V A L U E :: Akhlak Mulia, Amanah, Profesional, Kolaboratif, Unggul
            </div>
            <div class="hidden md:block italic">
                “Health Information Technology, Empowering Healthier Life.”
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
                                <a href="{{ route('lab.show', $cat->id) }}" class="block px-4 py-3 text-sm text-slate-700 hover:bg-blue-50 hover:text-blue-700 transition first:rounded-t-xl last:rounded-b-xl {{ $cat->id == $category->id ? 'bg-blue-50 text-blue-700 font-bold' : '' }}">
                                    {{ $cat->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                    <a href="{{ route('landing') }}#bulletins" class="text-slate-700 hover:text-blue-600 px-3 py-2 text-sm font-bold transition">Kabar Berita</a>
                    <a href="{{ route('landing') }}#stats" class="text-slate-700 hover:text-blue-600 px-3 py-2 text-sm font-bold transition">Statistik</a>
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

    <!-- Lab Hero -->
    <header class="relative py-24 overflow-hidden {{ $category->name == 'Data Engineering' ? 'bg-blue-600 text-white' : 'bg-slate-900 text-white' }}">
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-10"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="mb-8 inline-flex p-4 rounded-3xl bg-white/10 backdrop-blur-md">
                @if($category->name == 'Data Engineering')
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                @else
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                @endif
            </div>
            <h1 class="text-5xl font-black mb-6 tracking-tight">{{ $category->name }}</h1>
            <p class="text-xl opacity-90 max-w-2xl mx-auto font-medium leading-relaxed">
                @if($category->name == 'Data Engineering')
                    Membangun masa depan melalui pengolahan data cerdas dan arsitektur sistem informasi kesehatan digital yang inovatif.
                @else
                    Menjamin integritas dan keamanan data digital melalui investigasi forensik profesional berbasis nilai-nilai Islam.
                @endif
            </p>
        </div>
    </header>

    <!-- Lab Content -->
    <main class="py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-16">
                <!-- Left: Info -->
                <div class="lg:col-span-2">
                    <section class="mb-16">
                        <h2 class="text-3xl font-black text-slate-900 mb-8 flex items-center">
                            <span class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center mr-4 text-lg">1</span>
                            Profil Laboratorium
                        </h2>
                        <div class="prose prose-blue max-w-none text-slate-600 leading-relaxed text-lg">
                            @if($category->name == 'Data Engineering')
                                <p class="mb-6">Laboratorium Data Engineering PSTI UNISA Yogyakarta menyelenggarakan disiplin ilmu teknologi informasi yang berintegrasi pada bidang ilmu kesehatan melalui mata kuliah unggulan diantaranya Teknologi Informasi Rumah Sakit dan Inovasi Kesehatan Digital.</p>
                                <p>Fokus utama kami adalah melakukan perencanaan ide terhadap inovasi teknologi yang bercirikan KESEHATAN dan basis NILAI-NILAI ISLAM BERKEMAJUAN.</p>
                            @else
                                <p class="mb-6">Laboratorium Digital Forensik berfokus pada penerapan disiplin Ilmu Teknologi Informasi dan tren teknologi pada masa mendatang untuk investigasi dan keamanan data.</p>
                                <p>Kami menerapkan prinsip Akhlak Mulia, Amanah, Profesional, Kolaboratif, dan Unggul dalam setiap proses investigasi bukti digital untuk mendukung ekosistem kesehatan digital yang aman.</p>
                            @endif
                        </div>
                    </section>

                    <section>
                        <h2 class="text-3xl font-black text-slate-900 mb-8 flex items-center">
                            <span class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center mr-4 text-lg">2</span>
                            Berita Terbaru {{ $category->name }}
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            @forelse($bulletins as $bulletin)
                                <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm hover:shadow-xl transition-all duration-300">
                                    <div class="text-xs font-bold text-blue-500 mb-3 uppercase tracking-widest">{{ $bulletin->published_at?->format('d M Y') }}</div>
                                    <h3 class="text-xl font-black text-slate-900 mb-4 line-clamp-2 hover:text-blue-600 transition-colors">
                                        <a href="{{ route('lab.bulletins.show', ['category' => $category, 'bulletin' => $bulletin]) }}">{{ $bulletin->title }}</a>
                                    </h3>
                                    <div class="text-slate-500 text-sm mb-6 line-clamp-3 leading-relaxed">
                                        {!! strip_tags($bulletin->content) !!}
                                    </div>
                                    <a href="{{ route('lab.bulletins.show', ['category' => $category, 'bulletin' => $bulletin]) }}" class="text-sm font-black text-blue-600 hover:text-blue-800 flex items-center">
                                        BACA SELENGKAPNYA <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    </a>
                                </div>
                            @empty
                                <div class="col-span-2 py-12 text-center bg-slate-50 rounded-3xl border-2 border-dashed border-slate-200">
                                    <p class="text-slate-400 font-bold">Belum ada berita khusus untuk lab ini.</p>
                                </div>
                            @endforelse
                        </div>
                    </section>
                </div>

                <!-- Right: Sidebar -->
                <div class="space-y-12">
                    <!-- Stats Card -->
                    <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-xl">
                        <h3 class="text-xl font-black text-slate-900 mb-8">Statistik Lab</h3>
                        <div class="space-y-8">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center mr-4">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <div>
                                    <div class="text-2xl font-black text-slate-900">{{ $usageCount }}</div>
                                    <div class="text-xs font-bold text-slate-400 uppercase tracking-widest">Total Penggunaan</div>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center mr-4">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                                </div>
                                <div>
                                    <div class="text-2xl font-black text-slate-900">{{ count($bulletins) }}</div>
                                    <div class="text-xs font-bold text-slate-400 uppercase tracking-widest">Berita Terkait</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="bg-blue-600 p-8 rounded-[2.5rem] shadow-xl text-white">
                        <h3 class="text-xl font-black mb-6">Aktivitas Lab</h3>
                        <p class="text-blue-100 text-sm mb-8 leading-relaxed">Lakukan pencatatan aktivitas penggunaan laboratorium secara tertib.</p>
                        <a href="{{ route('login') }}" class="block w-full py-4 bg-white text-blue-600 text-center font-black rounded-2xl hover:bg-blue-50 transition shadow-lg">
                            CATAT PENGGUNAAN
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-white py-24 mt-24">
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
                    <li><a href="{{ route('landing') }}" class="hover:text-blue-400 transition">Halaman Utama</a></li>
                    <li><a href="{{ route('landing') }}#bulletins" class="hover:text-blue-400 transition">Kabar Berita</a></li>
                    <li><a href="{{ route('landing') }}#stats" class="hover:text-blue-400 transition">Statistik</a></li>
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
</body>
</html>
