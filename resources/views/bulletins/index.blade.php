<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold font-bold text-2xl text-slate-900 leading-tight">
                {{ __('Buletin') }}
            </h2>
            <a href="{{ route('bulletins.create') }}" class="inline-flex items-center px-6 py-2.5 bg-cyan-600 text-white font-black rounded-xl hover:bg-cyan-700 transition shadow-lg shadow-cyan-200 uppercase tracking-widest text-[10px]">
                Tambah Buletin
            </a>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="panel-shell">
            <div class="panel-card overflow-hidden">
                <div class="panel-card-head">
                    <p class="text-sm text-slate-500">Daftar informasi terbaru yang ditampilkan di halaman publik lab.</p>
                </div>
                <div class="panel-card-body">
                    @if(session('success'))
                        <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl relative" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="table-head">
                                <tr>
                                    <th>Judul</th>
                                    <th>Kategori</th>
                                    @if(auth()->user()->isAdmin())
                                        <th>Pembuat</th>
                                    @endif
                                    <th>Status ACC</th>
                                    <th>Tanggal Publish</th>
                                    <th class="text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-100">
                                @forelse($bulletins as $bulletin)
                                    <tr class="table-row hover:bg-slate-50/60 transition-colors">
                                        <td class="whitespace-nowrap">
                                            <div class="font-semibold text-slate-900">{{ $bulletin->title }}</div>
                                        </td>
                                        <td class="whitespace-nowrap">
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-black rounded-full bg-cyan-100 text-cyan-700">
                                                {{ $bulletin->category->name }}
                                            </span>
                                        </td>
                                        @if(auth()->user()->isAdmin())
                                            <td class="whitespace-nowrap">
                                                {{ $bulletin->user?->display_name ?? '-' }}
                                            </td>
                                        @endif
                                        <td class="whitespace-nowrap">
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-black rounded-full {{ $bulletin->approval_status === 'approved' ? 'bg-emerald-100 text-emerald-700' : ($bulletin->approval_status === 'rejected' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700') }}">
                                                {{ $bulletin->approval_status }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap">
                                            {{ $bulletin->published_at ? $bulletin->published_at->format('d M Y') : 'Draft' }}
                                        </td>
                                        <td class="whitespace-nowrap text-right text-sm font-medium space-x-3">
                                            <a href="{{ route('bulletins.edit', $bulletin) }}" class="text-cyan-700 hover:text-cyan-900 font-black">Edit</a>
                                            @if(auth()->user()->isAdmin() && $bulletin->approval_status === 'pending')
                                                <form action="{{ route('bulletins.approve', $bulletin) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-emerald-600 hover:text-emerald-800 font-black">ACC</button>
                                                </form>
                                                <form action="{{ route('bulletins.reject', $bulletin) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-rose-600 hover:text-rose-800 font-black" onclick="return confirm('Tolak buletin ini?')">Tolak</button>
                                                </form>
                                            @endif
                                            <form action="{{ route('bulletins.destroy', $bulletin) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-rose-600 hover:text-rose-800 font-black" onclick="return confirm('Yakin ingin menghapus?')">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ auth()->user()->isAdmin() ? 6 : 5 }}" class="px-6 py-8 whitespace-nowrap text-center text-slate-500">
                                            Belum ada buletin.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $bulletins->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
