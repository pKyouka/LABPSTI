<?php

namespace App\Http\Controllers;

use App\Models\LabUsageRecord;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LabUsageRecordController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = LabUsageRecord::with('category');

        if (! $user->isAdmin()) {
            $nim = $user->externalNim();

            if ($nim === null) {
                $query->whereRaw('1 = 0');
            } else {
                $query->where('user_id_number', $nim);
            }
        }

        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('date') && $request->date != '') {
            $query->whereDate('start_time', $request->date);
        }

        $records = $query->latest()->paginate(10);
        $categories = Category::all();

        return view('usage_records.index', compact('records', 'categories'));
    }

    public function create()
    {
        $user = Auth::user();
        $categories = Category::all();

        return view('usage_records.create', [
            'categories' => $categories,
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
            'category_id' => 'required|exists:categories,id',
            'start_time' => 'required|date',
            'end_time' => 'nullable|date|after_or_equal:start_time',
            'purpose' => 'required|string',
        ]);

        if (! $user->isAdmin()) {
            $nim = $user->externalNim();

            abort_if($nim === null, 403);

            $validated['user_name'] = $user->display_name;
            $validated['user_id_number'] = $nim;
        }

        LabUsageRecord::create($validated);

        return redirect()->route('usage_records.index')->with('success', 'Catatan penggunaan lab berhasil dibuat.');
    }

    public function destroy(LabUsageRecord $usage_record)
    {
        $user = Auth::user();

        if (! $user->isAdmin()) {
            $nim = $user->externalNim();
            abort_if($nim === null || (string) $usage_record->user_id_number !== $nim, 403);
        }

        $usage_record->delete();
        return redirect()->route('usage_records.index')->with('success', 'Catatan penggunaan lab berhasil dihapus.');
    }
}
