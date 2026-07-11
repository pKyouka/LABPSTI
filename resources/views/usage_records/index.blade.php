<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-lg font-semibold text-neutral-800">
                {{ __('Record Penggunaan Lab') }}
            </h2>
            <a href="{{ route('usage_records.create') }}" class="fluent-btn fluent-btn-primary fluent-btn-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Input Penggunaan
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if(session('success'))
            <div class="px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium rounded-xl">{{ session('success') }}</div>
        @endif

        <!-- Filter -->
        <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm p-5">
            <form action="{{ route('usage_records.index') }}" method="GET" class="flex flex-wrap gap-4 items-end">
                <div>
                    <x-input-label for="category_id" :value="__('Filter Lab')" />
                    <select name="category_id" id="category_id" class="fluent-select text-sm min-w-[180px]">
                        <option value="">Semua Lab</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="date" :value="__('Filter Tanggal')" />
                    <x-text-input id="date" type="date" name="date" :value="request('date')" />
                </div>
                <div class="flex items-center gap-2">
                    <x-primary-button type="submit" size="sm">Filter</x-primary-button>
                    <a href="{{ route('usage_records.index') }}" class="fluent-btn fluent-btn-ghost fluent-btn-sm">Reset</a>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-2xl border border-neutral-200 shadow-fluent-sm overflow-hidden">
            <table class="fluent-table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>NIM/ID</th>
                        <th>Lab</th>
                        <th>Waktu Mulai</th>
                        <th>Keperluan</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $record)
                        <tr>
                            <td class="font-medium text-neutral-800">{{ $record->user_name }}</td>
                            <td>{{ $record->user_id_number }}</td>
                            <td>{{ $record->category->name }}</td>
                            <td>{{ $record->start_time->format('d/m/Y H:i') }}</td>
                            <td class="max-w-[200px] truncate">{{ $record->purpose }}</td>
                            <td class="text-right">
                                <form action="{{ route('usage_records.destroy', $record) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="fluent-btn fluent-btn-danger fluent-btn-sm" onclick="return confirm('Yakin ingin menghapus?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-neutral-400">
                                <p class="text-sm font-medium">Belum ada record penggunaan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>{{ $records->links() }}</div>
    </div>
</x-app-layout>