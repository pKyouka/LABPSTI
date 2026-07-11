<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold text-neutral-800">{{ __('Edit User') }}</h2>
    </x-slot>

    <div class="space-y-6 max-w-xl">
        <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm p-6">
            <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-4">
                @csrf @method('PUT')
                <div><x-input-label for="name" :value="__('Nama')" /><x-text-input id="name" type="text" name="name" :value="old('name', $user->name)" required /><x-input-error :messages="$errors->get('name')" /></div>
                <div><x-input-label for="email" :value="__('Email')" /><x-text-input id="email" type="email" name="email" :value="old('email', $user->email)" required /><x-input-error :messages="$errors->get('email')" /></div>
                <div><x-input-label for="password" :value="__('Password (kosongkan jika tidak diubah)')" /><x-text-input id="password" type="password" name="password" /><x-input-error :messages="$errors->get('password')" /></div>
                <div><x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" /><x-text-input id="password_confirmation" type="password" name="password_confirmation" /><x-input-error :messages="$errors->get('password_confirmation')" /></div>
                <div><x-input-label for="role" :value="__('Role')" /><select id="role" name="role" class="fluent-select"><option value="mahasiswa" {{ old('role', $user->role)==='mahasiswa'?'selected':'' }}>Mahasiswa</option><option value="admin" {{ old('role', $user->role)==='admin'?'selected':'' }}>Admin</option></select><x-input-error :messages="$errors->get('role')" /></div>
                <div class="flex items-center gap-2 pt-2">
                    <x-primary-button type="submit" size="sm">Perbarui</x-primary-button>
                    <a href="{{ route('users.index') }}" class="fluent-btn fluent-btn-ghost fluent-btn-sm">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>