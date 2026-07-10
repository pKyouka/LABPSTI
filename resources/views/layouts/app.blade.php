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

        <!-- Prevent FOUC on sidebar state restoration -->
        <script>
            (function() {
                const collapsed = localStorage.getItem('sidebar-collapsed');
                if (collapsed === 'true' && window.innerWidth >= 1024) {
                    document.documentElement.classList.add('sb-collapsed');
                }
            })();
        </script>
    </head>
    <body class="font-roboto antialiased text-slate-900 bg-slate-50">
        <div class="relative min-h-screen">
            <!-- Fluent background blur effects -->
            <div aria-hidden="true" class="pointer-events-none fixed inset-0 overflow-hidden">
                <div class="absolute -top-40 -right-40 h-[500px] w-[500px] rounded-full bg-gradient-to-br from-cyan-300/20 via-blue-300/10 to-transparent blur-3xl"></div>
                <div class="absolute top-1/3 -left-40 h-[400px] w-[400px] rounded-full bg-gradient-to-tr from-blue-300/15 to-cyan-200/10 blur-3xl"></div>
                <div class="absolute bottom-0 right-1/4 h-[300px] w-[300px] rounded-full bg-gradient-to-t from-cyan-200/10 to-transparent blur-3xl"></div>
            </div>

            <!-- Backdrop Overlay (mobile only) -->
            <div id="sidebar-backdrop"
                 class="fixed inset-0 z-30 bg-slate-900/50 backdrop-blur-sm opacity-0 pointer-events-none lg:hidden transition-opacity duration-500 ease-[cubic-bezier(0.4,0,0.2,1)]"
                 onclick="toggleSidebar()"
                 aria-hidden="true"></div>

            <!-- Sidebar - Fluent Glass Design -->
            <aside id="sidebar"
                   class="fixed top-0 left-0 z-40 h-screen w-72 -translate-x-full sb-transition bg-white/70 backdrop-blur-2xl border-r border-white/20 shadow-2xl shadow-slate-900/5 flex flex-col">
                <!-- Sidebar Header with Gradient -->
                <div class="relative flex-shrink-0 px-5 py-5 overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-cyan-500 via-blue-500 to-indigo-600 opacity-95"></div>
                    <div class="absolute -top-10 -right-10 w-32 h-32 rounded-full bg-white/10 blur-xl"></div>
                    <div class="absolute -bottom-8 -left-8 w-24 h-24 rounded-full bg-white/5 blur-lg"></div>
                    <div class="relative flex items-center justify-between">
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center ring-1 ring-white/25 shadow-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                            </div>
                            <div>
                                <span class="text-lg font-black text-white tracking-tight block leading-tight">LAB<span class="text-cyan-200">PSTI</span></span>
                                <span class="text-[10px] font-semibold text-white/60 tracking-wider uppercase block">{{ Auth::user()->isAdmin() ? 'Admin Panel' : 'Portal' }}</span>
                            </div>
                        </a>
                        <button onclick="toggleSidebar()" class="lg:hidden p-1.5 rounded-lg text-white/60 hover:text-white hover:bg-white/10 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                </div>

                <!-- Search Bar -->
                <div class="px-4 pt-3 pb-2 flex-shrink-0">
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        <input type="text" placeholder="Cari menu..." class="w-full pl-9 pr-3 py-2 text-xs rounded-xl bg-slate-100/80 border border-slate-200/60 text-slate-600 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-cyan-400/30 focus:border-cyan-400 transition-all" oninput="filterSidebar(this.value)">
                    </div>
                </div>

                <!-- Navigation Items -->
                <div id="sidebar-nav" class="flex-1 overflow-y-auto px-3 py-2 space-y-0.5 scrollbar-thin scrollbar-thumb-slate-200 scrollbar-track-transparent">
                    @include('layouts.navigation')
                </div>

                <!-- Bottom User Card -->
                <div class="flex-shrink-0 p-3 border-t border-slate-200/50 bg-slate-50/50 backdrop-blur-sm">
                    <div class="flex items-center gap-3 px-3 py-2.5 rounded-2xl bg-white/70 backdrop-blur-sm border border-slate-200/50 shadow-sm">
                        <div class="relative flex-shrink-0">
                            <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-cyan-400 to-blue-600 text-white grid place-items-center text-sm font-black shadow-md shadow-cyan-200/50">
                                {{ strtoupper(substr(Auth::user()->display_name, 0, 1)) }}
                            </div>
                            <div class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-emerald-400 border-2 border-white rounded-full"></div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-slate-800 truncate">{{ Auth::user()->display_name }}</p>
                            <p class="text-[10px] font-medium text-slate-400 truncate">{{ Auth::user()->email }}</p>
                        </div>
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg>
                            </button>
                            <div x-show="open" @click.outside="open = false" class="absolute bottom-full right-0 mb-2 w-48 bg-white rounded-2xl shadow-xl shadow-slate-900/10 border border-slate-200/80 overflow-hidden backdrop-blur-xl" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    Profile
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold text-rose-600 hover:text-rose-700 hover:bg-rose-50 transition border-t border-slate-100">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                        Log Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Main Content Area -->
            <div id="main-content" class="sb-margin-transition lg:ml-72 relative z-10 min-h-screen flex flex-col">
                <!-- Top Bar - Fluent Style -->
                <div class="sticky top-0 z-20 bg-white/70 backdrop-blur-2xl border-b border-slate-200/50 shadow-sm shadow-slate-900/5">
                    <div class="flex items-center justify-between h-16 px-4 lg:px-8">
                        <div class="flex items-center gap-4">
                            {{-- Toggle Button -- visible on ALL screen sizes --}}
                            <button id="sidebar-toggle-btn"
                                    onclick="toggleSidebar()"
                                    title="Toggle sidebar"
                                    class="group p-2 -ml-2 rounded-xl hover:bg-slate-100 text-slate-400 hover:text-slate-700 transition-all focus:outline-none focus:ring-2 focus:ring-cyan-400/30">
                                <svg id="toggle-icon" class="w-5 h-5 transition-transform duration-500 ease-[cubic-bezier(0.4,0,0.2,1)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                </svg>
                            </button>
                            <div class="hidden sm:flex items-center gap-2 text-sm text-slate-400">
                                <span class="font-medium text-slate-800">{{ config('app.name', 'LabPSTI') }}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 bg-slate-100/80 rounded-xl text-xs text-slate-500">
                                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Page Heading -->
                @isset($header)
                    <header class="relative">
                        <div class="px-4 sm:px-6 lg:px-8 pt-6 sm:pt-7">
                            <div class="bg-white/70 backdrop-blur-xl rounded-2xl border border-white/50 shadow-lg shadow-slate-200/50 py-4 px-6 sm:px-8">
                                {{ $header }}
                            </div>
                        </div>
                    </header>
                @endisset

                <!-- Page Content -->
                <main class="flex-1 pb-10">
                    {{ $slot }}
                </main>
            </div>
        </div>

        <script>
            /**
             * Sidebar Toggle — Elegant show/hide with smooth animation.
             * Works on both desktop (content pushes) and mobile (overlay).
             * State persisted to localStorage.
             */
            (function() {
                const sidebar = document.getElementById('sidebar');
                const backdrop = document.getElementById('sidebar-backdrop');
                const mainContent = document.getElementById('main-content');
                const toggleIcon = document.getElementById('toggle-icon');

                const TRANSITION_CLASSES = [
                    'sb-transition',
                    'sb-margin-transition'
                ];

                /**
                 * Check if sidebar is currently collapsed (hidden).
                 */
                function isCollapsed() {
                    return sidebar.classList.contains('-translate-x-full');
                }

                /**
                 * Update toggle icon rotation based on sidebar state.
                 */
                function updateToggleIcon(collapsed) {
                    if (!toggleIcon) return;
                    // When collapsed, hamburger faces right (default).
                    // When expanded, slight rotation for visual feedback.
                    toggleIcon.style.transform = collapsed ? 'rotate(0deg)' : 'rotate(90deg)';
                }

                /**
                 * Show sidebar.
                 */
                function showSidebar(animate = true) {
                    if (!animate) {
                        // Temporarily disable transitions for instant state restoration
                        sidebar.classList.add('sb-no-transition');
                        mainContent.classList.add('sb-no-transition');
                    }

                    sidebar.classList.remove('-translate-x-full');
                    sidebar.setAttribute('aria-hidden', 'false');

                    // Desktop: push content
                    if (window.innerWidth >= 1024) {
                        mainContent.classList.add('lg:ml-72');
                        mainContent.classList.remove('lg:ml-0');
                    }

                    // Hide backdrop
                    backdrop.classList.add('opacity-0', 'pointer-events-none');
                    backdrop.classList.remove('opacity-100');
                    backdrop.setAttribute('aria-hidden', 'true');

                    localStorage.setItem('sidebar-collapsed', 'false');
                    document.documentElement.classList.remove('sb-collapsed');
                    updateToggleIcon(false);

                    if (!animate) {
                        // Force reflow then re-enable transitions
                        sidebar.offsetHeight;
                        mainContent.offsetHeight;
                        sidebar.classList.remove('sb-no-transition');
                        mainContent.classList.remove('sb-no-transition');
                    }
                }

                /**
                 * Hide sidebar.
                 */
                function hideSidebar(animate = true) {
                    if (!animate) {
                        sidebar.classList.add('sb-no-transition');
                        mainContent.classList.add('sb-no-transition');
                    }

                    sidebar.classList.add('-translate-x-full');
                    sidebar.setAttribute('aria-hidden', 'true');

                    // Desktop: collapse content to full width
                    mainContent.classList.remove('lg:ml-72');
                    mainContent.classList.add('lg:ml-0');

                    // Hide backdrop
                    backdrop.classList.add('opacity-0', 'pointer-events-none');
                    backdrop.classList.remove('opacity-100');
                    backdrop.setAttribute('aria-hidden', 'true');

                    localStorage.setItem('sidebar-collapsed', 'true');
                    document.documentElement.classList.add('sb-collapsed');
                    updateToggleIcon(true);

                    if (!animate) {
                        sidebar.offsetHeight;
                        mainContent.offsetHeight;
                        sidebar.classList.remove('sb-no-transition');
                        mainContent.classList.remove('sb-no-transition');
                    }
                }

                /**
                 * Toggle sidebar (called from button clicks).
                 */
                window.toggleSidebar = function() {
                    if (window.innerWidth < 1024) {
                        // Mobile: overlay behavior
                        const collapsed = isCollapsed();
                        if (collapsed) {
                            // Show sidebar as overlay
                            sidebar.classList.remove('-translate-x-full');
                            sidebar.setAttribute('aria-hidden', 'false');
                            backdrop.classList.remove('opacity-0', 'pointer-events-none');
                            backdrop.classList.add('opacity-100');
                            backdrop.setAttribute('aria-hidden', 'false');
                            updateToggleIcon(false);
                        } else {
                            // Hide overlay
                            sidebar.classList.add('-translate-x-full');
                            sidebar.setAttribute('aria-hidden', 'true');
                            backdrop.classList.add('opacity-0', 'pointer-events-none');
                            backdrop.classList.remove('opacity-100');
                            backdrop.setAttribute('aria-hidden', 'true');
                            updateToggleIcon(true);
                        }
                    } else {
                        // Desktop: push content behavior
                        if (isCollapsed()) {
                            showSidebar();
                        } else {
                            hideSidebar();
                        }
                    }
                };

                /**
                 * Initialize sidebar state on page load.
                 */
                function initialize() {
                    const collapsed = localStorage.getItem('sidebar-collapsed') === 'true';

                    if (window.innerWidth >= 1024) {
                        // Desktop: respect stored preference, default to visible
                        if (collapsed) {
                            hideSidebar(false);
                        } else {
                            showSidebar(false);
                        }
                    } else {
                        // Mobile: always start hidden
                        sidebar.classList.add('-translate-x-full');
                        sidebar.setAttribute('aria-hidden', 'true');
                        backdrop.classList.add('opacity-0', 'pointer-events-none');
                        backdrop.classList.remove('opacity-100');
                        backdrop.setAttribute('aria-hidden', 'true');
                        mainContent.classList.remove('lg:ml-72');
                        mainContent.classList.add('lg:ml-0');
                        updateToggleIcon(true);
                    }
                }

                /**
                 * Handle window resize.
                 */
                let resizeTimeout;
                window.addEventListener('resize', function() {
                    clearTimeout(resizeTimeout);
                    resizeTimeout = setTimeout(function() {
                        if (window.innerWidth >= 1024) {
                            // Transitioned to desktop
                            const collapsed = localStorage.getItem('sidebar-collapsed') === 'true';
                            if (collapsed) {
                                hideSidebar(false);
                            } else {
                                showSidebar(false);
                            }
                            // Ensure backdrop is hidden
                            backdrop.classList.add('opacity-0', 'pointer-events-none');
                            backdrop.classList.remove('opacity-100');
                            backdrop.setAttribute('aria-hidden', 'true');
                        } else {
                            // Transitioned to mobile — always hide sidebar overlay
                            sidebar.classList.add('-translate-x-full');
                            sidebar.setAttribute('aria-hidden', 'true');
                            backdrop.classList.add('opacity-0', 'pointer-events-none');
                            backdrop.classList.remove('opacity-100');
                            backdrop.setAttribute('aria-hidden', 'true');
                            mainContent.classList.remove('lg:ml-72');
                            mainContent.classList.add('lg:ml-0');
                            updateToggleIcon(true);
                        }
                    }, 150);
                });

                // Run on DOM ready
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', initialize);
                } else {
                    initialize();
                }
            })();
        </script>

        @stack('scripts')
    </body>
</html>