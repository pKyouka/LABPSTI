<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-display font-bold text-2xl text-slate-900 leading-tight">
                {{ __('Record Penggunaan Lab') }}
            </h2>
            <a href="{{ route('usage_records.create') }}" class="inline-flex items-center px-6 py-2.5 bg-cyan-600 text-white font-black rounded-xl hover:bg-cyan-700 transition shadow-lg shadow-cyan-200 uppercase tracking-widest text-[10px]">
                Input Penggunaan
            </a>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="panel-shell">
            <div class="panel-card overflow-hidden mb-6">
                <div class="panel-card-body">
                    <form action="{{ route('usage_records.index') }}" method="GET" class="flex flex-wrap gap-4 items-end">
                        <div>
                            <x-input-label for="category_id" :value="__('Filter Lab')" />
                            <select name="category_id" id="category_id" class="panel-select text-sm min-w-48">
                                <option value="">Semua Lab</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label for="date" :value="__('Filter Tanggal')" />
                            <x-text-input id="date" type="date" name="date" class="panel-input text-sm" :value="request('date')" />
                        </div>
                        <div>
                            <x-primary-button type="submit">Filter</x-primary-button>
                            <a href="{{ route('usage_records.index') }}" class="ml-2 text-sm text-slate-500 hover:text-slate-800 font-bold">Reset</a>
                        </div>
                    </form>
                </div>
            </div>

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
                                    <th>Nama</th>
                                    <th>NIM/ID</th>
                                    <th>Lab</th>
                                    <th>Waktu Mulai</th>
                                    <th>Keperluan</th>
                                    <th class="text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-100">
                                @forelse($records as $record)
                                    <tr class="table-row hover:bg-slate-50/60 transition-colors">
                                        <td class="whitespace-nowrap font-semibold text-slate-900">{{ $record->user_name }}</td>
                                        <td class="whitespace-nowrap">{{ $record->user_id_number }}</td>
                                        <td class="whitespace-nowrap">{{ $record->category->name }}</td>
                                        <td class="whitespace-nowrap">{{ $record->start_time->format('d/m/Y H:i') }}</td>
                                        <td>{{ Str::limit($record->purpose, 50) }}</td>
                                        <td class="whitespace-nowrap text-right text-sm font-medium">
                                            <form action="{{ route('usage_records.destroy', $record) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-rose-600 hover:text-rose-800 font-black" onclick="return confirm('Yakin ingin menghapus?')">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-8 whitespace-nowrap text-center text-slate-500">
                                            Belum ada record penggunaan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $records->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
