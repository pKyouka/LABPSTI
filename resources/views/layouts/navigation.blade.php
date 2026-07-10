{{-- Sidebar Navigation Vertical --}}
<nav class="space-y-1">
    @include('partials._nav_links')

    {{-- Divider --}}
    <div class="my-4 border-t border-slate-200"></div>

    {{-- User Info & Auth --}}
    <div class="px-3 py-3">
        <div class="flex items-center gap-3 mb-3 px-3">
            <div class="h-9 w-9 rounded-full bg-gradient-to-br from-cyan-500 to-blue-600 text-white grid place-items-center text-sm font-black shadow-lg shadow-cyan-200">
                {{ strtoupper(substr(Auth::user()->display_name, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-slate-800 truncate">{{ Auth::user()->display_name }}</p>
                <p class="text-[11px] font-semibold text-slate-500 truncate">{{ Auth::user()->email }}</p>
            </div>
        </div>

        <div class="space-y-1">
            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                <span>Profile</span>
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <a href="{{ route('logout') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold text-rose-600 hover:text-rose-700 hover:bg-rose-50 transition"
                   onclick="event.preventDefault(); this.closest('form').submit();">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    <span>Log Out</span>
                </a>
            </form>
        </div>
    </div>
</nav>
