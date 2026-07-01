{{-- Admin Reset Review & History --}}
@if(Auth::user()->isAdmin())
    @if($vmBorrow->reset_status !== "none")
        <div class="mt-4 px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl">
            <p class="text-xs font-black uppercase tracking-widest text-slate-500 mb-2">Reset History</p>
            <div class="text-xs text-slate-600 space-y-1">
                @if($vmBorrow->reset_requested_at)
                    <div>Diminta: <span class="font-semibold">{{ $vmBorrow->reset_requested_at->format('d M Y, H:i') }}</span></div>
                @endif
                @if($vmBorrow->reset_approved_at)
                    <div class="text-emerald-600">Disetujui: <span class="font-semibold">{{ $vmBorrow->reset_approved_at->format('d M Y, H:i') }}</span></div>
                @endif
                @if($vmBorrow->reset_rejected_at)
                    <div class="text-rose-600">Ditolak: <span class="font-semibold">{{ $vmBorrow->reset_rejected_at->format('d M Y, H:i') }}</span></div>
                    @if($vmBorrow->reset_rejection_reason)
                        <div class="text-rose-500 ml-4">Alasan: "{{ $vmBorrow->reset_rejection_reason }}"</div>
                    @endif
                @endif
            </div>
        </div>
    @endif

    @if($vmBorrow->reset_status === "pending")
        <div class="mt-4">
            <p class="text-sm text-slate-500 mb-2">Permintaan reset dari user:</p>
            @if($vmBorrow->reset_reason)
                <p class="text-sm text-slate-700 mb-3">"{{ $vmBorrow->reset_reason }}"</p>
            @endif
            <div class="flex gap-2">
                <form action="{{ route('vm_borrow.approve_reset', $vmBorrow) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-xs font-black rounded-xl">Acc Reset</button>
                </form>
                <form action="{{ route('vm_borrow.reject_reset', $vmBorrow) }}" method="POST" x-data="{ showReject: false }">
                    @csrf
                    <button type="button" @click="showReject = !showReject" class="px-4 py-2 bg-rose-600 text-white text-xs font-black rounded-xl">Tolak</button>
                    <div x-show="showReject" class="mt-2">
                        <textarea name="reset_rejection_reason" class="panel-textarea text-sm" rows="2" placeholder="Alasan tolak..." required></textarea>
                        <button type="submit" class="mt-1 px-4 py-1.5 bg-rose-700 text-white text-xs font-black rounded-xl">Konfirmasi Tolak</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endif