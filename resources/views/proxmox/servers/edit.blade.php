<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('proxmox.index', ['server' => $server->id]) }}" class="text-neutral-400 hover:text-neutral-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="text-lg font-semibold text-neutral-800">Edit Server · {{ $server->name }}</h2>
        </div>
    </x-slot>

    <div class="max-w-2xl space-y-4">
        <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm p-6">
            <form action="{{ route('proxmox.servers.update', $server) }}" method="POST">
                @csrf @method('PUT')
                @include('proxmox.servers._form', ['server' => $server, 'defaults' => []])
            </form>

            <div class="mt-4 pt-4 border-t border-neutral-100 flex items-center gap-2 flex-wrap">
                <form action="{{ route('proxmox.servers.test', $server) }}" method="POST">
                    @csrf
                    <button type="submit" class="fluent-btn fluent-btn-ghost fluent-btn-sm">Test Koneksi</button>
                </form>
                <form action="{{ route('proxmox.servers.destroy', $server) }}" method="POST" onsubmit="return confirm('Hapus server \'{{ $server->name }}\' dari database? Host/VM di Proxmox tidak terpengaruh.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="fluent-btn fluent-btn-ghost fluent-btn-sm text-red-600 hover:bg-red-50">Hapus Server</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
