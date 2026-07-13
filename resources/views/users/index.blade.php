<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-neutral-800">
                {{ __('Manajemen User') }}
            </h2>
            <a href="{{ route('users.create') }}" class="fluent-btn fluent-btn-primary fluent-btn-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah User
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('success'))
            <div class="px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium rounded-xl">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="px-4 py-3 bg-red-50 border border-red-200 text-red-700 text-sm font-medium rounded-xl">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm overflow-hidden">
            <div class="overflow-x-auto">
            <table class="fluent-table">
                <thead>
                    <tr>
                        <th class="w-12">No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                    <tr>
                        <td class="text-neutral-500">{{ $loop->iteration }}</td>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-accent to-accent-700 text-white grid place-items-center text-xs font-bold flex-shrink-0">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <span class="font-medium text-neutral-800">{{ $user->display_name }}</span>
                            </div>
                        </td>
                        <td class="text-neutral-600">{{ $user->email }}</td>
                        <td>
                            @if ($user->isAdmin())
                                <span class="fluent-badge fluent-badge-info">Admin</span>
                            @else
                                <span class="fluent-badge fluent-badge-neutral">Mahasiswa</span>
                            @endif
                        </td>
                        <td class="text-neutral-500 text-sm">{{ $user->created_at->format('d M Y') }}</td>
                        <td>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('users.edit', $user) }}" class="fluent-btn fluent-btn-ghost fluent-btn-sm">Edit</a>
                                @if ((int) $user->id !== (int) Auth::id())
                                <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="fluent-btn fluent-btn-danger fluent-btn-sm">Hapus</button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-neutral-400">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-10 h-10 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/></svg>
                                <p class="text-sm font-medium">Belum ada user.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>

        @if ($users->hasPages())
        <div>
            {{ $users->links() }}
        </div>
        @endif
    </div>
</x-app-layout>
