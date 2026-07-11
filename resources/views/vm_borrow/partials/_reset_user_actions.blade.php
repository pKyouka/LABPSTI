{{-- User Reset Request --}}
@if(!Auth::user()->isAdmin())
    @if($vmBorrow->reset_status === "none" || $vmBorrow->reset_status === "rejected")
        <form action="{{ route("vm_borrow.request_reset", $vmBorrow) }}" method="POST" class="flex gap-2 mt-4">
            @csrf
            <input type="text" name="reset_reason" placeholder="Alasan reset (opsional)" class="fluent-input flex-1 text-sm">
            <button type="submit" class="fluent-btn fluent-btn-sm" style="background:#8A6D00;color:#fff;border-color:#8A6D00">Request Reset VM</button>
        </form>
    @elseif($vmBorrow->reset_status === "pending")
        <div class="mt-4 px-4 py-3 bg-amber-50 border border-amber-200 rounded-xl text-sm font-medium text-amber-700">⏳ Permintaan reset sedang ditinjau admin.</div>
    @elseif($vmBorrow->reset_status === "approved")
        <div class="mt-4 px-4 py-3 bg-emerald-50 border border-emerald-200 rounded-xl text-sm font-medium text-emerald-700">✓ Reset disetujui. VM akan dibangun ulang.</div>
    @endif
@endif