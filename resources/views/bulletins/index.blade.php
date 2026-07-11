<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-lg font-semibold text-neutral-800">
                {{ __('Buletin') }}
            </h2>
            <a href="{{ route('bulletins.create') }}" class="fluent-btn fluent-btn-primary fluent-btn-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Buletin
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if(session('success'))
            <div class="px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium rounded-xl">{{ session('success') }}</div>
        @endif

        <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-neutral-100">
                <p class="text-sm text-neutral-500">Daftar informasi terbaru yang ditampilkan di halaman publik lab.</p>
            </div>
            <table class="fluent-table">
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Kategori</th>
                        @if(auth()->user()->isAdmin())
                            <th>Pembuat</th>
                        @endif
                        <th>Status ACC</th>
                        <th>Publish</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bulletins as $bulletin)
                        <tr>
                            <td class="font-medium text-neutral-800">{{ $bulletin->title }}</td>
                            <td>
                                <span class="fluent-badge fluent-badge-info">{{ $bulletin->category->name }}</span>
                            </td>
                            @if(auth()->user()->isAdmin())
                                <td>{{ $bulletin->user?->display_name ?? '-' }}</td>
                            @endif
                            <td>
                                @if($bulletin->approval_status === 'approved')
                                    <span class="fluent-badge fluent-badge-success">Approved</span>
                                @elseif($bulletin->approval_status === 'rejected')
                                    <span class="fluent-badge fluent-badge-danger">Rejected</span>
                                @else
                                    <span class="fluent-badge fluent-badge-warning">Pending</span>
                                @endif
                            </td>
                            <td>{{ $bulletin->published_at ? $bulletin->published_at->format('d M Y') : 'Draft' }}</td>
                            <td class="text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('bulletins.edit', $bulletin) }}" class="fluent-btn fluent-btn-ghost fluent-btn-sm">Edit</a>
                                    @if(auth()->user()->isAdmin() && $bulletin->approval_status === 'pending')
                                        <form action="{{ route('bulletins.approve', $bulletin) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="fluent-btn fluent-btn-primary fluent-btn-sm">ACC</button>
                                        </form>
                                        <form action="{{ route('bulletins.reject', $bulletin) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="fluent-btn fluent-btn-danger fluent-btn-sm" onclick="return confirm('Tolak buletin ini?')">Tolak</button>
                                        </form>
                                    @endif
                                    <form action="{{ route('bulletins.destroy', $bulletin) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="fluent-btn fluent-btn-danger fluent-btn-sm" onclick="return confirm('Yakin ingin menghapus?')">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->isAdmin() ? 6 : 5 }}" class="py-12 text-center text-neutral-400">
                                <p class="text-sm font-medium">Belum ada buletin.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>{{ $bulletins->links() }}</div>
    </div>
</x-app-layout>