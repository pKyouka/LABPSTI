<?php

namespace App\Http\Controllers;

use App\Models\Bulletin;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BulletinController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $query = Bulletin::with(['category', 'user'])->latest();

        if (! $user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        $bulletins = $query->paginate(10);

        return view('bulletins.index', compact('bulletins'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('bulletins.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category_id' => 'required|exists:categories,id',
            'published_at' => 'nullable|date',
        ]);

        $validated['content'] = strip_tags($validated['content']);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('bulletins', 'public');
            $validated['image'] = $path;
        }

        $validated['user_id'] = $user->id;

        if ($user->isAdmin()) {
            $validated['approval_status'] = 'approved';
            $validated['approved_by'] = $user->id;
            $validated['approved_at'] = now();
        } else {
            $validated['approval_status'] = 'pending';
            $validated['approved_by'] = null;
            $validated['approved_at'] = null;
            $validated['rejected_by'] = null;
            $validated['rejected_at'] = null;
            $validated['rejection_reason'] = null;
            $validated['published_at'] = null;
        }

        Bulletin::create($validated);

        $message = $user->isAdmin()
            ? 'Buletin berhasil dipublikasikan.'
            : 'Buletin berhasil diajukan dan menunggu ACC admin.';

        return redirect()->route('bulletins.index')->with('success', $message);
    }

    public function edit(Bulletin $bulletin)
    {
        $this->ensureCanManage($bulletin);

        $categories = Category::all();
        return view('bulletins.edit', compact('bulletin', 'categories'));
    }

    public function update(Request $request, Bulletin $bulletin)
    {
        $this->ensureCanManage($bulletin);
        $user = Auth::user();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category_id' => 'required|exists:categories,id',
            'published_at' => 'nullable|date',
        ]);

        $validated['content'] = strip_tags($validated['content']);

        if ($request->hasFile('image')) {
            if ($bulletin->image) {
                Storage::disk('public')->delete($bulletin->image);
            }
            $path = $request->file('image')->store('bulletins', 'public');
            $validated['image'] = $path;
        }

        if (! $user->isAdmin()) {
            $validated['approval_status'] = 'pending';
            $validated['approved_by'] = null;
            $validated['approved_at'] = null;
            $validated['rejected_by'] = null;
            $validated['rejected_at'] = null;
            $validated['rejection_reason'] = null;
            $validated['published_at'] = null;
        }

        $bulletin->update($validated);

        $message = $user->isAdmin()
            ? 'Buletin berhasil diperbarui.'
            : 'Perubahan buletin diajukan ulang dan menunggu ACC admin.';

        return redirect()->route('bulletins.index')->with('success', $message);
    }

    public function destroy(Bulletin $bulletin)
    {
        $this->ensureCanManage($bulletin);

        if ($bulletin->image) {
            Storage::disk('public')->delete($bulletin->image);
        }
        $bulletin->delete();
        return redirect()->route('bulletins.index')->with('success', 'Buletin berhasil dihapus.');
    }

    public function approve(Bulletin $bulletin)
    {
        if ($bulletin->approval_status !== 'pending') {
            return redirect()->route('bulletins.index')
                ->with('success', 'Buletin sudah diproses sebelumnya.');
        }

        $bulletin->update([
            'approval_status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'rejected_by' => null,
            'rejected_at' => null,
            'rejection_reason' => null,
            'published_at' => $bulletin->published_at ?? now(),
        ]);

        return redirect()->route('bulletins.index')
            ->with('success', 'Buletin berhasil di-ACC dan dipublikasikan.');
    }

    public function reject(Bulletin $bulletin)
    {
        if ($bulletin->approval_status !== 'pending') {
            return redirect()->route('bulletins.index')
                ->with('success', 'Buletin sudah diproses sebelumnya.');
        }

        $bulletin->update([
            'approval_status' => 'rejected',
            'rejected_by' => Auth::id(),
            'rejected_at' => now(),
            'approved_by' => null,
            'approved_at' => null,
            'published_at' => null,
        ]);

        return redirect()->route('bulletins.index')
            ->with('success', 'Buletin ditolak.');
    }

    private function ensureCanManage(Bulletin $bulletin): void
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return;
        }

        abort_if((int) $bulletin->user_id !== (int) $user->id, 403);
    }
}
