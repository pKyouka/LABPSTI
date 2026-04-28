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
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'category_id' => 'required|exists:categories,id',
            'published_at' => 'nullable|date',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('bulletins', 'public');
            $validated['image'] = $path;
        }

        $validated['user_id'] = Auth::id();

        Bulletin::create($validated);

        return redirect()->route('bulletins.index')->with('success', 'Bulletin created successfully.');
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

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'category_id' => 'required|exists:categories,id',
            'published_at' => 'nullable|date',
        ]);

        if ($request->hasFile('image')) {
            if ($bulletin->image) {
                Storage::disk('public')->delete($bulletin->image);
            }
            $path = $request->file('image')->store('bulletins', 'public');
            $validated['image'] = $path;
        }

        $bulletin->update($validated);

        return redirect()->route('bulletins.index')->with('success', 'Bulletin updated successfully.');
    }

    public function destroy(Bulletin $bulletin)
    {
        $this->ensureCanManage($bulletin);

        if ($bulletin->image) {
            Storage::disk('public')->delete($bulletin->image);
        }
        $bulletin->delete();
        return redirect()->route('bulletins.index')->with('success', 'Bulletin deleted successfully.');
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
