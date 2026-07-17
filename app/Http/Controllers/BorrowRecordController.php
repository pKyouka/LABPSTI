<?php

namespace App\Http\Controllers;

use App\Models\BorrowRecord;
use App\Models\InventoryItem;
use App\Traits\RequiresAdmin;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class BorrowRecordController extends Controller
{
    use RequiresAdmin;

    public function index(Request $request)
    {
        $user = Auth::user();
        $query = BorrowRecord::with('inventoryItem');

        if (! $user->isAdmin()) {
            $nim = $user->externalNim();

            if ($nim === null) {
                $query->whereRaw('1 = 0');
            } else {
                $query->where('user_id_number', $nim);
            }
        }

        if ($request->filled('inventory_item_id')) {
            $query->where('inventory_item_id', $request->integer('inventory_item_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('date')) {
            $query->whereDate('borrowed_at', $request->input('date'));
        }

        $records = $query->latest('borrowed_at')->paginate(10);
        $items = InventoryItem::orderBy('name')->get();

        return view('borrow_records.index', compact('records', 'items'));
    }

    public function create()
    {
        $user = Auth::user();
        $items = InventoryItem::where('is_active', true)->orderBy('name')->get();

        $categoryCards = [
            [
                'key' => 'Alat IoT Hidroponik',
                'label' => 'IoT Hidroponik',
                'description' => 'Alat pendukung hidroponik dan sensor terkait.',
            ],
            [
                'key' => 'Barang IoT',
                'label' => 'IoT',
                'description' => 'Komponen dan peralatan IoT untuk praktik dan eksperimen.',
            ],
            [
                'key' => 'Alat Jarkom',
                'label' => 'Jarkom',
                'description' => 'Perangkat jaringan dan alat pendukung konfigurasi.',
            ],
        ];

        return view('borrow_records.create', [
            'items' => $items,
            'groupedItems' => $items->groupBy('category'),
            'categoryCards' => $categoryCards,
            'isAdmin' => $user->isAdmin(),
            'prefillName' => $user->display_name,
            'prefillIdNumber' => $user->externalNim(),
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'user_name' => 'required|string|max:255',
            'user_id_number' => 'required|string|max:255',
            'inventory_item_id' => 'required|exists:inventory_items,id',
            'quantity' => 'required|integer|min:1',
            'borrowed_at' => 'required|date',
            'due_at' => 'nullable|date|after_or_equal:borrowed_at',
            'purpose' => 'required|string',
            'notes' => 'nullable|string',
            'request_pdf' => 'required|file|mimes:pdf|max:5120',
        ]);

        $isAdmin = $user->isAdmin();

        if (! $user->isAdmin()) {
            $nim = $user->externalNim();

            abort_if($nim === null, 403);

            $validated['user_name'] = $user->display_name;
            $validated['user_id_number'] = $nim;
        }

        DB::transaction(function () use ($validated, $isAdmin, $user) {
            $item = InventoryItem::whereKey($validated['inventory_item_id'])->lockForUpdate()->firstOrFail();
            $requestPdfPath = $validated['request_pdf']->store('borrow-records', 'local');

            if ($isAdmin && $item->available_stock < (int) $validated['quantity']) {
                throw ValidationException::withMessages([
                    'quantity' => 'Stok barang tidak mencukupi.',
                ]);
            }

            BorrowRecord::create([
                'inventory_item_id' => $item->id,
                'user_name' => $validated['user_name'],
                'user_id_number' => $validated['user_id_number'],
                'quantity' => (int) $validated['quantity'],
                'borrowed_at' => $validated['borrowed_at'],
                'due_at' => $validated['due_at'] ?? null,
                'purpose' => $validated['purpose'],
                'notes' => $validated['notes'] ?? null,
                'request_pdf_path' => $requestPdfPath,
                'status' => $isAdmin ? 'dipinjam' : 'menunggu',
                'approval_status' => $isAdmin ? 'approved' : 'pending',
                'approved_by' => $isAdmin ? $user->id : null,
                'approved_at' => $isAdmin ? now() : null,
            ]);

            if ($isAdmin) {
                $item->decrement('available_stock', (int) $validated['quantity']);
            }
        });

        $message = $isAdmin
            ? 'Peminjaman berhasil dicatat.'
            : 'Peminjaman diajukan dan menunggu ACC admin.';

        return redirect()->route('borrow_records.index')->with('success', $message);
    }

    public function approve(BorrowRecord $borrowRecord)
    {
        $this->ensureAdmin();

        $validated = request()->validate([
            'ready_at' => 'required|date',
            'pickup_at' => 'required|date|after_or_equal:ready_at',
        ]);

        DB::transaction(function () use ($borrowRecord, $validated) {
            if ($borrowRecord->approval_status !== 'pending') {
                return;
            }

            $item = InventoryItem::whereKey($borrowRecord->inventory_item_id)->lockForUpdate()->firstOrFail();

            if ($item->available_stock < (int) $borrowRecord->quantity) {
                throw ValidationException::withMessages([
                    'status' => 'Stok barang tidak mencukupi untuk ACC peminjaman ini.',
                ]);
            }

            $borrowRecord->update([
                'approval_status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'ready_at' => $validated['ready_at'],
                'pickup_at' => $validated['pickup_at'],
                'rejected_by' => null,
                'rejected_at' => null,
                'rejection_reason' => null,
                'status' => 'dipinjam',
            ]);

            $item->decrement('available_stock', (int) $borrowRecord->quantity);
        });

        $borrowRecord->refresh()->loadMissing('inventoryItem');
        $approvalPdf = $this->generateApprovalPdf($borrowRecord);

        $borrowRecord->update([
            'approval_pdf_path' => $approvalPdf['path'],
            'approval_pdf_md5' => $approvalPdf['md5'],
            'approval_pdf_sha256' => $approvalPdf['sha256'],
        ]);

        return redirect()->route('borrow_records.index')->with('success', 'Peminjaman berhasil di-ACC.');
    }

    public function reject(BorrowRecord $borrowRecord)
    {
        $this->ensureAdmin();

        if ($borrowRecord->approval_status !== 'pending') {
            return redirect()->route('borrow_records.index')->with('success', 'Status peminjaman sudah diproses sebelumnya.');
        }

        $borrowRecord->update([
            'approval_status' => 'rejected',
            'approved_by' => null,
            'approved_at' => null,
            'rejected_by' => Auth::id(),
            'rejected_at' => now(),
            'status' => 'ditolak',
        ]);

        return redirect()->route('borrow_records.index')->with('success', 'Peminjaman ditolak.');
    }

    public function markReturned(BorrowRecord $borrowRecord)
    {
        $user = Auth::user();

        if (! $user->isAdmin()) {
            $nim = $user->externalNim();

            abort_if($nim === null || (string) $borrowRecord->user_id_number !== $nim, 403);
        }

        abort_if($borrowRecord->approval_status !== 'approved', 422, 'Peminjaman belum di-ACC admin.');

        DB::transaction(function () use ($borrowRecord) {
            $borrowRecord->loadMissing('inventoryItem');

            if ($borrowRecord->returned_at !== null) {
                return;
            }

            $item = InventoryItem::whereKey($borrowRecord->inventory_item_id)->lockForUpdate()->firstOrFail();

            $borrowRecord->returned_at = now();
            $borrowRecord->status = 'dikembalikan';
            $borrowRecord->save();

            $item->increment('available_stock', (int) $borrowRecord->quantity);
        });

        return redirect()->route('borrow_records.index')->with('success', 'Barang sudah dikembalikan.');
    }

    private function generateApprovalPdf(BorrowRecord $borrowRecord): array
    {
        $pdf = Pdf::loadView('borrow_records.pdf', [
            'record' => $borrowRecord,
        ])->setPaper('a4');

        $path = 'borrow-records/approval-'.$borrowRecord->id.'-'.now()->format('YmdHis').'.pdf';
        $output = $pdf->output();
        $md5 = md5($output);
        $sha256 = hash('sha256', $output);

        Storage::disk('local')->put($path, $output);

        return [
            'path' => $path,
            'md5' => $md5,
            'sha256' => $sha256,
        ];
    }

    public function downloadRequestPdf(BorrowRecord $borrowRecord)
    {
        $this->ensureCanAccessBorrowRecord($borrowRecord);
        abort_if(! $borrowRecord->request_pdf_path, 404);

        return $this->downloadBorrowPdf($borrowRecord->request_pdf_path, 'request-'.$borrowRecord->id.'.pdf');
    }

    public function downloadApprovalPdf(BorrowRecord $borrowRecord)
    {
        $this->ensureCanAccessBorrowRecord($borrowRecord);
        abort_if(! $borrowRecord->approval_pdf_path, 404);

        return $this->downloadBorrowPdf($borrowRecord->approval_pdf_path, 'approval-'.$borrowRecord->id.'.pdf');
    }

    private function ensureCanAccessBorrowRecord(BorrowRecord $borrowRecord): void
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return;
        }

        $nim = $user->externalNim();
        abort_if($nim === null || (string) $borrowRecord->user_id_number !== $nim, 403);
    }

    private function downloadBorrowPdf(string $path, string $downloadName)
    {
        // Prefer private disk; fall back to legacy public files during migration.
        if (Storage::disk('local')->exists($path)) {
            return Storage::disk('local')->download($path, $downloadName);
        }

        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->download($path, $downloadName);
        }

        abort(404);
    }
}
