<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-2xl text-slate-900 leading-tight">
                {{ __('Manajemen User') }}
            </h2>
            <a href="{{ route('users.create') }}" class="inline-flex items-center px-4 py-2 bg-cyan-600 text-white font-bold rounded-xl hover:bg-cyan-700 transition shadow-lg shadow-cyan-200 text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah User
            </a>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="panel-shell">
            @if (session('success'))
                <div class="mb-6 px-6 py-4 bg-emerald-50 border border-emerald-200 text-emerald-700 font-medium rounded-2xl">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 px-6 py-4 bg-red-50 border border-red-200 text-red-700 font-medium rounded-2xl">
                    {{ session('error') }}
                </div>
            @endif

            <div class="panel-card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50/50">
                                <th class="text-left py-4 px-6 text-xs font-black text-slate-400 uppercase tracking-wider">No</th>
                                <th class="text-left py-4 px-6 text-xs font-black text-slate-400 uppercase tracking-wider">Nama</th>
                                <th class="text-left py-4 px-6 text-xs font-black text-slate-400 uppercase tracking-wider">Email</th>
                                <th class="text-left py-4 px-6 text-xs font-black text-slate-400 uppercase tracking-wider">Role</th>
                                <th class="text-left py-4 px-6 text-xs font-black text-slate-400 uppercase tracking-wider">Dibuat</th>
                                <th class="text-left py-4 px-6 text-xs font-black text-slate-400 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($users as $user)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-4 px-6 font-medium text-slate-600">{{ $loop->iteration }}</td>
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-3">
                                        <div class="h-9 w-9 rounded-full bg-gradient-to-br from-cyan-500 to-blue-600 text-white grid place-items-center text-xs font-black flex-shrink-0">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-semibold text-slate-800">{{ $user->display_name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-slate-600">{{ $user->email }}</td>
                                <td class="py-4 px-6">
                                    @if ($user->isAdmin())
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-700">Admin</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-700">Mahasiswa</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-slate-500 text-xs">{{ $user->created_at->format('d M Y') }}</td>
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('users.edit', $user) }}" class="inline-flex items-center px-3 py-1.5 bg-amber-50 text-amber-700 font-bold rounded-lg hover:bg-amber-100 transition text-xs">
                                            Edit
                                        </a>
                                        @if ((int) $user->id !== (int) Auth::id())
                                        <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-700 font-bold rounded-lg hover:bg-red-100 transition text-xs">
                                                Hapus
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-500">
                                    <div class="flex flex-col items-center gap-2">
                                        <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/></svg>
                                        <p class="font-medium">Belum ada user.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($users->hasPages())
            <div class="mt-6">
                {{ $users->links() }}
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
