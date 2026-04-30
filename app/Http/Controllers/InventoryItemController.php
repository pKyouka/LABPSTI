<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryItemController extends Controller
{
    public function index()
    {
        $items = InventoryItem::withCount('borrowRecords')->latest()->paginate(10);

        return view('inventory_items.index', compact('items'));
    }

    public function show(InventoryItem $inventoryItem)
    {
        $inventoryItem->loadCount('borrowRecords');

        return view('inventory_items.show', [
            'item' => $inventoryItem,
        ]);
    }

    public function create()
    {
        $user = Auth::user();

        abort_unless($user->isAdmin(), 403);

        return view('inventory_items.create');
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        abort_unless($user->isAdmin(), 403);

        $validated = $request->validate([
            'code' => 'nullable|string|max:100|unique:inventory_items,code',
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'unit' => 'nullable|string|max:50',
            'total_stock' => 'required|integer|min:1',
            'available_stock' => 'nullable|integer|min:0|lte:total_stock',
            'location' => 'nullable|string|max:255',
            'condition' => 'nullable|string|max:100',
            'is_active' => 'nullable|boolean',
            'return_to_borrow' => 'nullable|boolean',
        ]);

        $code = $validated['code'] ?? null;

        if ($code === null || trim($code) === '') {
            $baseCode = str((string) ($validated['category'] ?? $validated['name']))
                ->append('-', $validated['name'])
                ->slug()
                ->toString();
            $code = $baseCode;

            $suffix = 2;

            while (InventoryItem::where('code', $code)->exists()) {
                $code = $baseCode.'-'.$suffix;
                $suffix++;
            }
        }

        $validated['available_stock'] = $validated['available_stock'] ?? $validated['total_stock'];
        $validated['code'] = $code;
        $validated['unit'] = $validated['unit'] ?? 'unit';
        $validated['condition'] = $validated['condition'] ?? 'baik';
        $validated['is_active'] = $request->boolean('is_active', true);

        InventoryItem::create($validated);

        if ($request->boolean('return_to_borrow')) {
            return redirect()->route('borrow_records.create')->with('success', 'Barang baru berhasil ditambahkan dan bisa langsung dipilih.');
        }

        return redirect()->route('inventory_items.index')->with('success', 'Barang berhasil ditambahkan.');
    }

    public function destroy(InventoryItem $inventoryItem)
    {
        $user = Auth::user();

        abort_unless($user->isAdmin(), 403);

        if ($inventoryItem->borrowRecords()->exists()) {
            return redirect()->route('inventory_items.show', $inventoryItem)
                ->with('error', 'Barang tidak bisa dihapus karena sudah memiliki riwayat peminjaman.');
        }

        $inventoryItem->delete();

        return redirect()->route('inventory_items.index')->with('success', 'Barang berhasil dihapus.');
    }
}
