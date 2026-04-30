<?php

namespace App\Http\Controllers;

use App\Models\BorrowRecord;
use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BorrowRecordController extends Controller
{
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
        $user = Auth::user();

        abort_unless($user->isAdmin(), 403);

        DB::transaction(function () use ($borrowRecord, $user) {
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
                'approved_by' => $user->id,
                'approved_at' => now(),
                'rejected_by' => null,
                'rejected_at' => null,
                'rejection_reason' => null,
                'status' => 'dipinjam',
            ]);

            $item->decrement('available_stock', (int) $borrowRecord->quantity);
        });

        return redirect()->route('borrow_records.index')->with('success', 'Peminjaman berhasil di-ACC.');
    }

    public function reject(BorrowRecord $borrowRecord)
    {
        $user = Auth::user();

        abort_unless($user->isAdmin(), 403);

        if ($borrowRecord->approval_status !== 'pending') {
            return redirect()->route('borrow_records.index')->with('success', 'Status peminjaman sudah diproses sebelumnya.');
        }

        $borrowRecord->update([
            'approval_status' => 'rejected',
            'approved_by' => null,
            'approved_at' => null,
            'rejected_by' => $user->id,
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
}
