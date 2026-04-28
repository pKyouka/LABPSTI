<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-5">
        <p class="text-[11px] font-black uppercase tracking-[0.2em] text-cyan-600">TEKNOLOGI INFORMASI</p>
        <h1 class="font-display text-2xl font-bold text-slate-900 mt-1">Masuk Ke Portal Lab</h1>
        <p class="text-sm text-slate-500 mt-1">Akses dashboard sesuai role Anda: admin atau mahasiswa.</p>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Nomor Induk / Email -->
        <div>
            <x-input-label for="n" :value="__('NIM Mahasiswa / Email Admin')" />
            <x-text-input id="n" class="panel-input" type="text" name="n" :value="old('n')" required autofocus autocomplete="username" placeholder="Masukkan NIM atau email admin" />
            <x-input-error :messages="$errors->get('n')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="panel-input"
                            type="password"
                            name="p"
                            required autocomplete="current-password"
                            placeholder="Masukkan kata sandi" />

            <x-input-error :messages="$errors->get('p')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-cyan-600 shadow-sm focus:ring-cyan-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">Remember me</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-slate-500 hover:text-slate-800 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500" href="{{ route('password.request') }}">
                    Forgot your password?
                </a>
            @endif

            <x-primary-button class="ms-3">
                Log in
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
