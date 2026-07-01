{{-- Reset History Timeline --}}
<div class="mt-4">
    <div class="flex items-center gap-2 mb-2">
        <p class="text-xs font-black uppercase tracking-widest text-slate-500">Reset History</p>
    </div>
    <div class="text-xs text-slate-600 space-y-1">
        <div>VM dibuat: <span class="font-semibold">{{ $vmBorrow->created_at->format('d M Y, H:i') }}</span></div>
        @if($vmBorrow->reset_requested_at)
            <div>Reset diminta: <span class="font-semibold">{{ $vmBorrow->reset_requested_at->format('d M Y, H:i') }}</span></div>
        @endif
        @if($vmBorrow->reset_approved_at)
            <div class="text-emerald-600">Reset disetujui: <span class="font-semibold">{{ $vmBorrow->reset_approved_at->format('d M Y, H:i') }}</span></div>
        @endif
        @if($vmBorrow->reset_rejected_at)
            <div class="text-rose-600">Reset ditolak: <span class="font-semibold">{{ $vmBorrow->reset_rejected_at->format('d M Y, H:i') }}</span></div>
            @if($vmBorrow->reset_rejection_reason)
                <div class="text-rose-500 ml-4">Alasan: "{{ $vmBorrow->reset_rejection_reason }}"</div>
            @endif
        @endif
    </div>
</div>