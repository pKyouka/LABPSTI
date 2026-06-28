<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-4">
            <h2 class="font-display font-bold text-2xl text-slate-900 leading-tight">
                {{ __('Peminjaman Virtual Machine') }}
            </h2>
            @if(!Auth::user()->isAdmin())
                <a href="{{ route('vm_borrow.create') }}" class="inline-flex items-center px-6 py-2.5 bg-cyan-600 text-white font-black rounded-xl hover:bg-cyan-700 transition shadow-lg shadow-cyan-200 uppercase tracking-widest text-[10px]">
                    Ajukan VM
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="panel-shell">
            <div class="panel-card overflow-hidden">
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
                            <tbody class="bg-white divide-y divide-slate-100">
                                @forelse($requests as $req)
                                    <tr class="table-row hover:bg-slate-50/60 transition-colors">
                                        @if(Auth::user()->isAdmin())
                                            <td class="whitespace-nowrap font-semibold text-slate-900">{{ $req->user->display_name }}</td>
                                        @endif
                                        <td class="whitespace-nowrap">
                                            <span class="inline-flex items-center gap-1.5">
                                                <span class="font-semibold text-slate-800 capitalize">{{ $req->os_type }}</span>
                                                <span class="text-slate-500">{{ $req->os_version }}</span>
                                            </span>
                                        </td>
                                        <td class="max-w-[180px] truncate" title="{{ $req->purpose }}">{{ $req->purpose }}</td>
                                        <td class="whitespace-nowrap">{{ $req->start_date->format('d/m/Y') }}</td>
                                        <td class="whitespace-nowrap">{{ $req->duration_days }} hari</td>
                                        <td class="whitespace-nowrap">{{ $req->end_date?->format('d/m/Y') ?? '-' }}</td>
                                        <td class="whitespace-nowrap">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-widest
                                                {{ $req->status === 'approved' ? 'bg-emerald-50 text-emerald-700' : ($req->status === 'rejected' ? 'bg-rose-50 text-rose-700' : 'bg-amber-50 text-amber-700') }}">
                                                {{ $req->status === 'approved' ? 'Disetujui' : ($req->status === 'rejected' ? 'Ditolak' : 'Menunggu') }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('vm_borrow.show', $req) }}"
                                               class="text-cyan-700 hover:text-cyan-900 font-black">
                                                {{ Auth::user()->isAdmin() && $req->status === 'pending' ? 'Tinjau →' : 'Detail →' }}
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ Auth::user()->isAdmin() ? 8 : 7 }}" class="px-6 py-8 whitespace-nowrap text-center text-slate-500">
                                            Belum ada permintaan peminjaman VM.
                                            @if(!Auth::user()->isAdmin())
                                                <a href="{{ route('vm_borrow.create') }}" class="ml-1 text-cyan-600 hover:underline font-semibold">Ajukan sekarang →</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $requests->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>