<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-5">
        <p class="text-[11px] font-semibold uppercase tracking-[0.15em] text-accent">TEKNOLOGI INFORMASI</p>
        <h1 class="text-xl font-semibold text-neutral-800 mt-1">Masuk Ke Portal Lab</h1>
        <p class="text-sm text-neutral-500 mt-1">Akses sebagai admin atau mahasiswa.</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf
        <div>
            <x-input-label for="n" :value="__('NIM Mahasiswa / Email Admin')" />
            <x-text-input id="n" type="text" name="n" :value="old('n')" required autofocus autocomplete="username" placeholder="Masukkan NIM atau email admin" />
            <x-input-error :messages="$errors->get('n')" />
        </div>
        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" type="password" name="p" required autocomplete="current-password" placeholder="Masukkan kata sandi" />
            <x-input-error :messages="$errors->get('p')" />
        </div>
        <div class="flex items-center gap-2">
            <input id="remember_me" type="checkbox" class="fluent-checkbox" name="remember">
            <label for="remember_me" class="text-sm text-neutral-600">Remember me</label>
        </div>
        <div class="flex items-center justify-between pt-2">
            @if (Route::has('password.request'))
                <a class="text-sm text-accent hover:text-accent-hover hover:underline" href="{{ route('password.request') }}">Forgot password?</a>
            @endif
            <x-primary-button class="ms-3" size="sm">Log in</x-primary-button>
        </div>
    </form>
</x-guest-layout>