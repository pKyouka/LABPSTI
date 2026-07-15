{{-- Service Requests — untuk VM yang sudah approved --}}
<div class="rounded-xl border border-accent-200 bg-accent-light/20 p-4 space-y-4">
    <div class="flex items-center justify-between">
        <h4 class="text-sm font-semibold text-accent-dark flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            Service Requests
        </h4>
        @if(!Auth::user()->isAdmin())
            <button type="button" onclick="document.getElementById('serviceRequestForm').classList.toggle('hidden')"
                class="fluent-btn fluent-btn-primary fluent-btn-sm">+ Request Baru</button>
        @endif
    </div>

    @if(!Auth::user()->isAdmin())
    {{-- Form Request Baru (mahasiswa) --}}
    <div id="serviceRequestForm" class="hidden rounded-lg border border-emerald-200 bg-white p-4 space-y-3">
        <h5 class="text-sm font-semibold text-emerald-700">Ajukan Permintaan Layanan</h5>
        <form action="{{ route('vm_borrow.service_request.store', $vmBorrow) }}" method="POST" x-data="serviceRequestForm()">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <x-input-label for="service_category" value="Kategori" />
                    <select id="service_category" name="category" x-model="category"
                        class="mt-1 block w-full rounded-lg border-neutral-200 shadow-sm focus:border-accent focus:ring focus:ring-accent/20 text-sm text-neutral-700">
                        <option value="">-- Pilih Kategori --</option>
                        <option value="open_port">Buka Port</option>
                        <option value="install_software">Install Software</option>
                        <option value="edit_config">Edit Konfigurasi</option>
                        <option value="add_storage">Tambah Storage</option>
                        <option value="add_ram">Tambah RAM</option>
                        <option value="add_cpu">Tambah CPU Core</option>
                        <option value="reset_vm">Reset VM</option>
                        <option value="other">Lainnya</option>
                    </select>
                    <x-input-error :messages="$errors->get('category')" />
                </div>
                <div>
                    <x-input-label for="service_title" value="Judul" />
                    <x-text-input id="service_title" type="text" name="title" required placeholder="Contoh: Buka Port 8080" />
                    <x-input-error :messages="$errors->get('title')" />
                </div>
                {{-- Field kondisional berdasarkan kategori --}}
                <div x-show="category === 'open_port'">
                    <x-input-label for="port_number" value="Nomor Port" />
                    <x-text-input id="port_number" type="number" name="port_number" min="1" max="65535" placeholder="8080" />
                </div>
                <div x-show="category === 'install_software'">
                    <x-input-label for="software_name" value="Nama Software" />
                    <x-text-input id="software_name" type="text" name="software_name" placeholder="docker, nginx, dll." />
                </div>
                <div x-show="category === 'edit_config'">
                    <x-input-label for="config_path" value="Path Konfigurasi" />
                    <x-text-input id="config_path" type="text" name="config_path" placeholder="/etc/nginx/nginx.conf" />
                </div>
            </div>
            <div>
                <x-input-label for="service_description" value="Deskripsi Detail" />
                <textarea id="service_description" name="description" rows="3" class="fluent-textarea" required placeholder="Jelaskan permintaan Anda secara detail..."></textarea>
                <x-input-error :messages="$errors->get('description')" />
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('serviceRequestForm').classList.add('hidden')" class="fluent-btn fluent-btn-ghost fluent-btn-sm">Batal</button>
                <button type="submit" class="fluent-btn fluent-btn-primary fluent-btn-sm">Kirim Request</button>
            </div>
        </form>
        <script>
            function serviceRequestForm() {
                return { category: '' }
            }
        </script>
    </div>
    @endif

    {{-- List Service Requests --}}
    @if($vmBorrow->serviceRequests->isEmpty())
        <p class="text-sm text-neutral-500 text-center py-3">Belum ada service request pada VM ini.</p>
    @else
        <div class="space-y-3">
            @foreach($vmBorrow->serviceRequests as $req)
            <div class="rounded-lg border bg-white p-4 @if($req->status === 'rejected') border-red-200 bg-red-50/50 @elseif($req->status === 'completed') border-emerald-200 bg-emerald-50/50 @elseif($req->status === 'in_progress') border-blue-200 bg-blue-50/50 @else border-neutral-200 @endif">
                <div class="flex items-start justify-between gap-3">
                    <div class="space-y-1 flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="fluent-badge text-xs {{ $req->statusBadgeClass() }}">{{ $req->statusLabel() }}</span>
                            <span class="text-[10px] font-semibold uppercase tracking-wider text-neutral-500">{{ $req->categoryLabel() }}</span>
                            <span class="text-xs text-neutral-400">{{ $req->created_at->diffForHumans() }}</span>
                        </div>
                        <h5 class="text-sm font-semibold text-neutral-800">{{ $req->title }}</h5>
                        <p class="text-sm text-neutral-600 whitespace-pre-wrap">{{ $req->description }}</p>
                        <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-neutral-500 mt-1">
                            @if($req->port_number)
                                <span>Port: <code class="text-accent font-mono">{{ $req->port_number }}</code></span>
                            @endif
                            @if($req->software_name)
                                <span>Software: <code class="text-accent font-mono">{{ $req->software_name }}</code></span>
                            @endif
                            @if($req->config_path)
                                <span>Config Path: <code class="text-accent font-mono">{{ $req->config_path }}</code></span>
                            @endif
                        </div>
                        @if($req->admin_response)
                            <div class="mt-2 rounded-lg bg-neutral-100 border border-neutral-200 p-3">
                                <p class="text-[10px] font-semibold uppercase tracking-wider text-neutral-500 mb-1">Respons Admin</p>
                                <p class="text-sm text-neutral-700 whitespace-pre-wrap">{{ $req->admin_response }}</p>
                            </div>
                        @endif
                    </div>

                    {{-- Admin Actions --}}
                    @if(Auth::user()->isAdmin() && !in_array($req->status, ['completed', 'rejected']))
                    <div class="flex-shrink-0">
                        <button type="button" onclick="document.getElementById('adminResponse{{ $req->id }}').classList.toggle('hidden')"
                            class="fluent-btn fluent-btn-sm {{ $req->status === 'pending' ? 'fluent-btn-primary' : 'fluent-btn-ghost' }}">
                            {{ $req->status === 'pending' ? 'Tangani' : 'Update' }}
                        </button>
                    </div>
                    @endif
                </div>

                {{-- Admin Response Form --}}
                @if(Auth::user()->isAdmin() && !in_array($req->status, ['completed', 'rejected']))
                <div id="adminResponse{{ $req->id }}" class="hidden mt-3 border-t pt-3">
                    <form action="{{ route('vm_borrow.service_request.update', ['vmBorrow' => $vmBorrow, 'serviceRequest' => $req]) }}" method="POST" class="space-y-3">
                        @csrf
                        <div class="flex gap-2">
                            <select name="status" class="text-sm rounded-lg border-neutral-200 shadow-sm focus:border-accent focus:ring focus:ring-accent/20 text-neutral-700">
                                <option value="in_progress" @if($req->status === 'in_progress') selected @endif>Diproses</option>
                                <option value="completed">Selesai</option>
                                <option value="rejected">Tolak</option>
                            </select>
                        </div>
                        <div>
                            <textarea name="admin_response" rows="2" class="fluent-textarea text-sm" placeholder="Respons / catatan untuk peminjam..."></textarea>
                        </div>
                        <div class="flex justify-end gap-2">
                            <button type="button" onclick="this.closest('.hidden').classList.add('hidden')" class="fluent-btn fluent-btn-ghost fluent-btn-sm">Batal</button>
                            <button type="submit" class="fluent-btn fluent-btn-primary fluent-btn-sm">Simpan</button>
                        </div>
                    </form>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    @endif
</div>