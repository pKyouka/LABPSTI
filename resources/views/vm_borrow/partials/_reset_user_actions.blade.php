{{-- User Reset Request --}}
@if(!Auth::user()->isAdmin())
    @if($vmBorrow->reset_status === "none" || $vmBorrow->reset_status === "rejected")
        <form action="{{ route("vm_borrow.request_reset", $vmBorrow) }}" method="POST" class="mt-4">
            @csrf
            <div class="flex gap-2">
                <input type="text" name="reset_reason" placeholder="Alasan reset (opsional)" class="panel-input flex-1 text-sm">
                <button type="submit" class="px-4 py-2 bg-amber-600 text-white text-xs font-black rounded-xl">Request Reset VM</button>
            </div>
        </form>
    @elseif($vmBorrow->reset_status === "pending")
        <div class="mt-4 px-4 py-3 bg-amber-50 border border-amber-200 rounded-xl">
            <p class="text-amber-700 text-sm font-semibold">⏳ Permintaan reset sedang ditinjau admin.</p>
        </div>
    @elseif($vmBorrow->reset_status === "approved")
        <div class="mt-4 px-4 py-3 bg-emerald-50 border border-emerald-200 rounded-xl">
            <p class="text-emerald-700 text-sm font-semibold">✓ Reset disetujui. VM akan dibangun ulang.</p>
        </div>
    @endif
@endif