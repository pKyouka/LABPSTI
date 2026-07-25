<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'LABPSTI') }}</title>
        @include('partials.favicon')

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=roboto:400,500,600,700&display=swap" rel="stylesheet" />

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
    <body class="bg-[#F3F3F3] text-[#1F1F1F] antialiased">
        <div class="relative min-h-screen">
            <!-- Mica Background -->
            <div aria-hidden="true" class="pointer-events-none fixed inset-0 -z-10 fluent-mica"></div>

            <!-- Backdrop Overlay (mobile) -->
            <div id="sidebar-backdrop"
                 class="fixed inset-0 z-30 opacity-0 pointer-events-none lg:hidden transition-opacity duration-300"
                 onclick="toggleSidebar()"
                 aria-hidden="true"></div>

            <!-- Sidebar — Fluent Acrylic -->
            <aside id="sidebar"
                   class="fixed top-0 left-0 z-40 h-full -translate-x-full sb-transition flex flex-col">
                <!-- Sidebar Header -->
                <div id="sidebar-header-gradient" class="relative flex-shrink-0 px-4 py-4 overflow-hidden">
                    <div class="absolute -top-8 -right-8 w-24 h-24 rounded-full bg-white/10 blur-xl"></div>
                    <div class="absolute -bottom-6 -left-6 w-20 h-20 rounded-full bg-white/5 blur-lg"></div>
                    <div class="relative flex items-center justify-between">
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 min-w-0">
                            <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                            </div>
                            <div class="sb-brand-text overflow-hidden">
                                <span class="text-sm font-bold text-white tracking-tight block leading-tight whitespace-nowrap">LAB<span class="text-blue-200">PSTI</span></span>
                                <span class="text-[9px] font-semibold text-white/50 tracking-wider uppercase block whitespace-nowrap">{{ Auth::user()->isAdmin() ? 'Admin' : 'Portal' }}</span>
                            </div>
                        </a>
                        <button onclick="toggleSidebar()" class="lg:hidden p-2 rounded-lg text-white/70 hover:text-white hover:bg-white/10 transition" aria-label="Tutup sidebar">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                </div>

                <!-- Search Bar -->
                <div class="px-3 pt-2 pb-1 flex-shrink-0 sb-search-wrapper">
                    <div class="relative">
                        <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        <input type="text" placeholder="Cari menu..." class="w-full pl-8 pr-3 py-1.5 text-xs rounded-md bg-black/[0.04] border border-transparent text-neutral-700 placeholder:text-neutral-400 focus:outline-none focus:bg-white focus:border-accent focus:ring-[3px] focus:ring-accent/15 transition-all" oninput="filterSidebar(this.value)">
                    </div>
                </div>

                <!-- Navigation Items -->
                <div id="sidebar-nav" class="flex-1 overflow-y-auto px-2 py-1 space-y-0.5">
                    @include('layouts.navigation')
                </div>

                <!-- Bottom User Card -->
                <div class="flex-shrink-0 p-2 border-t border-black/5 bg-white/30">
                    <div class="flex items-center gap-2.5 px-2.5 py-2 rounded-lg bg-black/[0.02] border border-black/5">
                        <div class="relative flex-shrink-0">
                            <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-accent to-accent-700 text-white grid place-items-center text-xs font-bold shadow-sm">
                                {{ strtoupper(substr(Auth::user()->display_name, 0, 1)) }}
                            </div>
                            <div class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-emerald-400 border-2 border-white rounded-full"></div>
                        </div>
                        <div class="flex-1 min-w-0 sb-user-info">
                            <p class="text-xs font-semibold text-neutral-800 truncate">{{ Auth::user()->display_name }}</p>
                            <p class="text-[10px] text-neutral-400 truncate">{{ Auth::user()->email }}</p>
                        </div>
                        <div class="relative sb-user-menu-btn" x-data="{ open: false }">
                            <button @click="open = !open" class="p-1 rounded-md text-neutral-400 hover:text-neutral-600 hover:bg-black/5 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg>
                            </button>
                            <div x-show="open" x-cloak @click.outside="open = false" class="absolute bottom-full right-0 mb-2 w-44 bg-white rounded-xl shadow-fluent-lg border border-neutral-200 overflow-hidden" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-neutral-600 hover:bg-neutral-50 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    Profile
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm text-[#E81123] hover:bg-red-50 transition border-t border-neutral-100">
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
            <div id="main-content" class="sb-margin-transition relative z-10 min-h-screen flex flex-col">
                <!-- Top Bar — Fluent Acrylic -->
                <div class="sticky top-0 z-20 fluent-topbar flex items-center justify-between px-5">
                    <div class="flex items-center gap-3">
                        <button id="sidebar-toggle-btn"
                                onclick="toggleSidebar()"
                                title="Toggle sidebar"
                                class="p-1.5 -ml-1 rounded-md text-neutral-500 hover:text-neutral-700 hover:bg-black/5 transition focus:outline-none focus:ring-2 focus:ring-accent/30">
                            <svg id="toggle-icon" class="w-5 h-5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                        <div class="hidden sm:block">
                            <span class="text-sm font-semibold text-neutral-700">{{ config('app.name', 'LabPSTI') }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="hidden sm:inline-flex items-center gap-1.5 px-2.5 py-1 text-xs text-neutral-500 bg-black/[0.04] rounded-md">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                            {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
                        </span>

                        {{-- Notification Bell (live poll, no full-page reload needed) --}}
                        <div
                            class="relative"
                            x-data="notificationBell(@js(route('notifications.feed')), @js(route('notifications.markAllRead')), @js(csrf_token()))"
                            x-init="init()"
                            @click.outside="open = false"
                        >
                            <button @click="open = !open; if (open) refresh()" type="button" class="relative p-2 rounded-md text-neutral-500 hover:text-neutral-700 hover:bg-black/5 transition" title="Notifikasi">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                <span
                                    x-show="unreadCount > 0"
                                    x-text="unreadCount > 99 ? '99+' : unreadCount"
                                    x-cloak
                                    class="absolute -top-0.5 -right-0.5 min-w-4 h-4 px-1 bg-red-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center"
                                ></span>
                            </button>

                            <div
                                x-show="open"
                                x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                                class="absolute right-0 top-full mt-2 w-80 sm:w-96 bg-white rounded-xl shadow-fluent-lg border border-neutral-200 overflow-hidden max-h-[calc(100vh-12rem)] flex flex-col"
                                style="display: none;"
                            >
                                <div class="px-4 py-3 border-b border-neutral-100 flex items-center justify-between flex-shrink-0">
                                    <h3 class="text-sm font-semibold text-neutral-800">Notifikasi</h3>
                                    <button
                                        type="button"
                                        @click="markAllRead()"
                                        :disabled="unreadCount === 0 || marking"
                                        class="text-[10px] font-medium text-accent hover:text-accent-hover disabled:opacity-40"
                                    >Tandai semua dibaca</button>
                                </div>
                                <div class="overflow-y-auto flex-1">
                                    <template x-if="loading && items.length === 0">
                                        <div class="px-4 py-8 text-center">
                                            <p class="text-xs text-neutral-400">Memuat…</p>
                                        </div>
                                    </template>
                                    <template x-if="!loading && items.length === 0">
                                        <div class="px-4 py-8 text-center">
                                            <svg class="w-8 h-8 text-neutral-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                            <p class="text-xs text-neutral-400">Belum ada notifikasi</p>
                                        </div>
                                    </template>
                                    <template x-for="n in items" :key="n.id">
                                        <form :action="n.read_url" method="POST" class="block border-b border-neutral-50 last:border-b-0">
                                            <input type="hidden" name="_token" :value="csrf">
                                            <button type="submit" class="w-full text-left px-4 py-3 hover:bg-neutral-50 transition">
                                                <div class="flex gap-2.5">
                                                    <span
                                                        class="w-2 h-2 rounded-full flex-shrink-0 mt-1.5"
                                                        :class="n.is_unread ? 'bg-red-500' : 'bg-neutral-300'"
                                                    ></span>
                                                    <div class="min-w-0 flex-1">
                                                        <p class="text-xs font-medium" :class="n.is_unread ? 'text-neutral-800' : 'text-neutral-600'" x-text="n.title"></p>
                                                        <p class="text-[10px] text-neutral-400 mt-0.5">
                                                            <span x-show="n.subtitle" x-text="n.subtitle + ' · '"></span>
                                                            <span x-text="n.created_at"></span>
                                                        </p>
                                                    </div>
                                                </div>
                                            </button>
                                        </form>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Page Heading -->
                @isset($header)
                    <header class="px-5 pt-5 pb-1">
                        <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm px-6 py-4">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <!-- Page Content -->
                <main class="flex-1 px-5 pb-8 pt-4">
                    {{ $slot }}
                </main>
            </div>
        </div>

        <script>
            (function() {
                const sidebar = document.getElementById('sidebar');
                const backdrop = document.getElementById('sidebar-backdrop');
                const toggleIcon = document.getElementById('toggle-icon');
                const html = document.documentElement;
                const breakpoint = 1024;

                let isMobileOverlayOpen = false;

                function isDesktop() {
                    return window.innerWidth >= breakpoint;
                }

                function isCollapsed() {
                    return html.classList.contains('sb-collapsed');
                }

                function updateToggleIcon(collapsed) {
                    if (!toggleIcon) return;
                    toggleIcon.style.transform = collapsed ? 'rotate(0deg)' : 'rotate(90deg)';
                }

                function hideBackdrop() {
                    if (!backdrop) return;
                    backdrop.classList.add('opacity-0', 'pointer-events-none');
                    backdrop.classList.remove('opacity-100');
                    backdrop.setAttribute('aria-hidden', 'true');
                }

                function expandDesktop() {
                    sidebar.classList.remove('-translate-x-full');
                    sidebar.setAttribute('aria-hidden', 'false');
                    hideBackdrop();
                    html.classList.remove('sb-collapsed');
                    localStorage.setItem('sidebar-collapsed', 'false');
                    updateToggleIcon(false);
                }

                function collapseDesktop() {
                    sidebar.classList.remove('-translate-x-full');
                    sidebar.setAttribute('aria-hidden', 'false');
                    hideBackdrop();
                    html.classList.add('sb-collapsed');
                    localStorage.setItem('sidebar-collapsed', 'true');
                    updateToggleIcon(true);
                }

                function showMobileOverlay() {
                    sidebar.classList.remove('-translate-x-full');
                    sidebar.setAttribute('aria-hidden', 'false');
                    if (backdrop) {
                        backdrop.classList.remove('opacity-0', 'pointer-events-none');
                        backdrop.classList.add('opacity-100');
                        backdrop.setAttribute('aria-hidden', 'false');
                    }
                    document.body.classList.add('overflow-hidden');
                    isMobileOverlayOpen = true;
                    updateToggleIcon(false);
                }

                function hideMobileOverlay() {
                    sidebar.classList.add('-translate-x-full');
                    sidebar.setAttribute('aria-hidden', 'true');
                    hideBackdrop();
                    document.body.classList.remove('overflow-hidden');
                    isMobileOverlayOpen = false;
                    updateToggleIcon(true);
                }

                function initDesktop() {
                    isMobileOverlayOpen = false;
                    sidebar.classList.remove('-translate-x-full');
                    sidebar.setAttribute('aria-hidden', 'false');
                    hideBackdrop();
                    document.body.classList.remove('overflow-hidden');

                    const savedCollapsed = localStorage.getItem('sidebar-collapsed');
                    if (savedCollapsed === 'true') {
                        html.classList.add('sb-collapsed');
                        updateToggleIcon(true);
                    } else {
                        html.classList.remove('sb-collapsed');
                        updateToggleIcon(false);
                    }
                }

                function initMobile() {
                    html.classList.remove('sb-collapsed');
                    sidebar.classList.add('-translate-x-full');
                    sidebar.setAttribute('aria-hidden', 'true');
                    hideBackdrop();
                    document.body.classList.remove('overflow-hidden');
                    isMobileOverlayOpen = false;
                    updateToggleIcon(true);
                }

                window.toggleSidebar = function() {
                    if (!isDesktop()) {
                        if (isMobileOverlayOpen) {
                            hideMobileOverlay();
                        } else {
                            showMobileOverlay();
                        }
                    } else {
                        if (isCollapsed()) {
                            expandDesktop();
                        } else {
                            collapseDesktop();
                        }
                    }
                };

                window.filterSidebar = function(query) {
                    const nav = document.getElementById('sidebar-nav');
                    if (!nav) return;
                    const links = nav.querySelectorAll('.sb-nav-link-inner');
                    const categories = nav.querySelectorAll('.sb-category-label');
                    const q = (query || '').toLowerCase().trim();

                    if (!q) {
                        // Show all
                        links.forEach(l => { l.style.display = ''; });
                        categories.forEach(c => { c.style.display = ''; });
                        return;
                    }

                    let visibleInSection = false;
                    let currentSection = null;

                    links.forEach(link => {
                        const text = (link.textContent || '').toLowerCase();
                        const matches = text.includes(q);

                        if (matches) {
                            link.style.display = '';
                            visibleInSection = true;
                            // Show the category label above this link
                            let prev = link.previousElementSibling;
                            while (prev) {
                                if (prev.classList.contains('sb-category-label')) {
                                    prev.style.display = '';
                                    break;
                                }
                                if (prev.classList.contains('sb-nav-link-inner')) break;
                                prev = prev.previousElementSibling;
                            }
                        } else {
                            link.style.display = 'none';
                        }
                    });

                    // Hide category labels with no visible links
                    categories.forEach(cat => {
                        let next = cat.nextElementSibling;
                        let hasVisible = false;
                        while (next && !next.classList.contains('sb-category-label')) {
                            if (next.classList.contains('sb-nav-link-inner') && next.style.display !== 'none') {
                                hasVisible = true;
                                break;
                            }
                            next = next.nextElementSibling;
                        }
                        cat.style.display = hasVisible ? '' : 'none';
                    });
                };

                // Initialize on load
                function initialize() {
                    if (isDesktop()) {
                        initDesktop();
                    } else {
                        initMobile();
                    }
                }

                let resizeTimeout;
                window.addEventListener('resize', function() {
                    clearTimeout(resizeTimeout);
                    resizeTimeout = setTimeout(function() {
                        if (isDesktop()) {
                            initDesktop();
                        } else {
                            isMobileOverlayOpen = false;
                            document.body.classList.remove('overflow-hidden');
                            initMobile();
                        }
                    }, 150);
                });

                // Close mobile sidebar on Escape key
                window.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && isMobileOverlayOpen) {
                        hideMobileOverlay();
                    }
                });

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