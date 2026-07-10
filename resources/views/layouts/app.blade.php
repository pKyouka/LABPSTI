<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>
        @include('partials.favicon')

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=roboto:400,500,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-roboto antialiased text-slate-900">
        <div class="relative min-h-screen overflow-x-clip bg-slate-100">
            <div aria-hidden="true" class="pointer-events-none fixed inset-x-0 top-0 h-80 bg-gradient-to-b from-cyan-100 via-blue-50 to-transparent"></div>
            <div aria-hidden="true" class="pointer-events-none fixed -top-20 -right-24 h-72 w-72 rounded-full bg-cyan-200/50 blur-3xl"></div>
            <div aria-hidden="true" class="pointer-events-none fixed top-48 -left-24 h-64 w-64 rounded-full bg-blue-200/40 blur-3xl"></div>

            <!-- Mobile Overlay -->
            <div id="sidebar-backdrop" class="fixed inset-0 bg-slate-900/50 z-30 hidden lg:hidden" onclick="toggleSidebar()"></div>

            <!-- Sidebar -->
            <aside id="sidebar" class="fixed top-0 left-0 z-40 h-screen w-72 -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out bg-white/95 backdrop-blur-xl border-r border-slate-200 shadow-2xl shadow-slate-900/10">
                <!-- Sidebar Header -->
                <div class="flex items-center justify-between h-16 px-6 border-b border-slate-200 bg-gradient-to-r from-cyan-600 to-blue-600">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                        </div>
                        <span class="text-lg font-black text-white tracking-tight">{{ config('app.name', 'LabPSTI') }}</span>
                    </a>
                    <!-- Close button (mobile) -->
                    <button onclick="toggleSidebar()" class="lg:hidden text-white/80 hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Navigation -->
                <div class="overflow-y-auto h-[calc(100vh-4rem)] py-4 px-3">
                    @include('layouts.navigation')
                </div>
            </aside>

            <!-- Main Content -->
            <div id="main-content" class="transition-all duration-300 lg:ml-72 relative z-10">
                <!-- Top Bar (mobile) -->
                <div class="sticky top-0 z-20 lg:hidden bg-white/90 backdrop-blur-xl border-b border-slate-200 shadow-sm">
                    <div class="flex items-center justify-between h-16 px-4">
                        <button onclick="toggleSidebar()" class="p-2 rounded-lg hover:bg-slate-100 text-slate-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        </button>
                        <span class="text-sm font-black text-slate-700">{{ config('app.name', 'LabPSTI') }}</span>
                        <div class="w-10"></div>
                    </div>
                </div>

                <!-- Page Heading -->
                @isset($header)
                    <header class="relative">
                        <div class="px-4 sm:px-6 lg:px-8 pt-4 sm:pt-6 lg:pt-8">
                            <div class="bg-white/80 backdrop-blur-xl rounded-2xl border border-slate-200 shadow-lg shadow-slate-200/50 py-5 px-6 sm:px-8">
                                {{ $header }}
                            </div>
                        </div>
                    </header>
                @endisset

                <!-- Page Content -->
                <main class="pb-10">
                    {{ $slot }}
                </main>
            </div>
        </div>

        <script>
            function toggleSidebar() {
                const sidebar = document.getElementById('sidebar');
                const backdrop = document.getElementById('sidebar-backdrop');
                sidebar.classList.toggle('-translate-x-full');
                backdrop.classList.toggle('hidden');
            }

            // Close sidebar on resize to desktop
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 1024) {
                    document.getElementById('sidebar').classList.remove('-translate-x-full');
                    document.getElementById('sidebar-backdrop').classList.add('hidden');
                }
            });
        </script>

        @stack('scripts')
    </body>
</html>
