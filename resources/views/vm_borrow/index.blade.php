<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-4">
            <h2 class="text-lg font-semibold text-neutral-800">
                {{ __('Peminjaman Virtual Machine') }}
            </h2>
            @if(!Auth::user()->isAdmin())
                <a href="{{ route('vm_borrow.create') }}" class="fluent-btn fluent-btn-primary fluent-btn-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    Ajukan VM
                </a>
            @endif
        </div>
    </x-slot>

    <div class="space-y-6">
        @if(session('success'))
            <div class="px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium rounded-xl">{{ session('success') }}</div>
        @endif

        <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm overflow-hidden">
            <table class="fluent-table">
                <thead>
                    <tr>
                        @if(Auth::user()->isAdmin())
                            <th>Peminjam</th>
                        @endif
                        <th>Sistem Operasi</th>
                        <th>Keperluan</th>
                        <th>Mulai</th>
                        <th>Durasi</th>
                        <th>Selesai</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                        <tr>
                            @if(Auth::user()->isAdmin())
                                <td class="font-medium text-neutral-800">{{ $req->user->display_name }}</td>
                            @endif
                            <td>
                                <span class="font-medium text-neutral-800 capitalize">{{ $req->os_type }}</span>
                                <span class="text-neutral-500"> {{ $req->os_version }}</span>
                            </td>
                            <td class="max-w-[200px] truncate" title="{{ $req->purpose }}">{{ $req->purpose }}</td>
                            <td>{{ $req->start_date->format('d/m/Y') }}</td>
                            <td>{{ $req->duration_days }} hari</td>
                            <td>{{ $req->end_date?->format('d/m/Y') ?? '-' }}</td>
                            <td>
                                <div class="flex flex-col gap-1">
                                    @if($req->status === 'approved')
                                        <span class="fluent-badge fluent-badge-success">Disetujui</span>
                                    @elseif($req->status === 'rejected')
                                        <span class="fluent-badge fluent-badge-danger">Ditolak</span>
                                    @else
                                        <span class="fluent-badge fluent-badge-warning">Menunggu</span>
                                    @endif
                                    @if($req->status === 'approved' && $req->reset_status !== 'none')
                                        <span class="fluent-badge {{ $req->reset_status === 'pending' ? 'fluent-badge-warning' : ($req->reset_status === 'approved' ? 'fluent-badge-success' : 'fluent-badge-danger') }}">
                                            {{ $req->reset_status === 'pending' ? 'Reset Pending' : ($req->reset_status === 'approved' ? 'Reset OK' : 'Reset Ditolak') }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="text-right">
                                <a href="{{ route('vm_borrow.show', $req) }}" class="fluent-btn fluent-btn-ghost fluent-btn-sm">
                                    {{ Auth::user()->isAdmin() && $req->status === 'pending' ? 'Tinjau →' : 'Detail →' }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ Auth::user()->isAdmin() ? 8 : 7 }}" class="py-12 text-center text-neutral-400">
                                <p class="text-sm font-medium">Belum ada permintaan peminjaman VM.</p>
                                @if(!Auth::user()->isAdmin())
                                    <a href="{{ route('vm_borrow.create') }}" class="text-accent font-medium hover:underline">Ajukan sekarang →</a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>{{ $requests->links() }}</div>
    </div>
</x-app-layout>