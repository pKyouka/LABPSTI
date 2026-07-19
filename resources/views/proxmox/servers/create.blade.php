<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('proxmox.index') }}" class="text-neutral-400 hover:text-neutral-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="text-lg font-semibold text-neutral-800">Tambah Server Proxmox</h2>
        </div>
    </x-slot>

    <div class="max-w-2xl">
        <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm p-6">
            <form action="{{ route('proxmox.servers.store') }}" method="POST">
                @csrf
                @include('proxmox.servers._form', ['server' => null, 'defaults' => $defaults])
            </form>
        </div>
    </div>
</x-app-layout>
