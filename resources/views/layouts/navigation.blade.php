<nav x-data="{ open: false }" class="relative z-20 pt-4 pb-3">
    <!-- Primary Navigation Menu -->
    <div class="panel-shell">
        <div class="panel-card px-4 sm:px-6">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex flex-col">
                        <span class="font-display text-lg font-black text-slate-900 leading-tight tracking-tight uppercase">LAB<span class="text-cyan-600">PSTI</span></span>
                        <span class="text-[10px] font-black text-slate-500 tracking-[0.24em] uppercase">{{ Auth::user()->isAdmin() ? 'Admin Panel' : 'Portal Mahasiswa' }}</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden sm:flex sm:items-center sm:ms-10 sm:space-x-2">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('bulletins.index')" :active="request()->routeIs('bulletins.*')">
                        {{ __('Buletin') }}
                    </x-nav-link>
                    <x-nav-link :href="route('usage_records.index')" :active="request()->routeIs('usage_records.*')">
                        {{ __('Record Penggunaan') }}
                    </x-nav-link>
                    <x-nav-link :href="route('inventory_items.index')" :active="request()->routeIs('inventory_items.*')">
                        {{ __('Barang & Alat') }}
                    </x-nav-link>
                    <x-nav-link :href="route('borrow_records.index')" :active="request()->routeIs('borrow_records.*')">
                        {{ __('Peminjaman') }}
                    </x-nav-link>
                    <x-nav-link :href="route('vm_borrow.index')" :active="request()->routeIs('vm_borrow.*')">
                        {{ __('VM') }}
                    </x-nav-link>
                    @if(Auth::user()->isAdmin())
                    <x-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')">
                        {{ __('Laporan') }}
                    </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2 px-3 py-2 border border-slate-200 text-sm leading-4 font-bold rounded-xl text-slate-600 bg-white/80 hover:text-slate-900 focus:outline-none transition duration-150">
                            <div class="h-8 w-8 rounded-full bg-gradient-to-br from-cyan-500 to-blue-600 text-white grid place-items-center text-xs font-black">
                                {{ strtoupper(substr(Auth::user()->display_name, 0, 1)) }}
                            </div>
                            <div>{{ Auth::user()->display_name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-xl text-slate-500 hover:text-slate-700 hover:bg-slate-100 focus:outline-none focus:bg-slate-100 focus:text-slate-700 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="panel-shell mt-2">
        <div class="panel-card overflow-hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('bulletins.index')" :active="request()->routeIs('bulletins.*')">
                {{ __('Buletin') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('usage_records.index')" :active="request()->routeIs('usage_records.*')">
                {{ __('Record Penggunaan') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('inventory_items.index')" :active="request()->routeIs('inventory_items.*')">
                {{ __('Barang & Alat') }}
            </x-responsive-nav-link>
             <x-responsive-nav-link :href="route('borrow_records.index')" :active="request()->routeIs('borrow_records.*')">
                 {{ __('Peminjaman') }}
             </x-responsive-nav-link>
             <x-responsive-nav-link :href="route('vm_borrow.index')" :active="request()->routeIs('vm_borrow.*')">
                 {{ __('VM') }}
             </x-responsive-nav-link>
             @if(Auth::user()->isAdmin())
             <x-responsive-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')">
                 {{ __('Laporan') }}
             </x-responsive-nav-link>
             @endif
         </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-slate-200">
            <div class="px-4">
                <div class="font-semibold text-base text-slate-800">{{ Auth::user()->display_name }}</div>
                <div class="font-medium text-sm text-slate-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
        </div>
        </div>
    </div>
</nav>
