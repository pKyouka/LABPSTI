<?php

namespace App\Http\Controllers;

use App\Models\Bulletin;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
            'content' => 'required|string|max:20000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'category_id' => 'required|exists:categories,id',
            'published_at' => 'nullable|date',
        ]);

        $validated['content'] = strip_tags($validated['content']);

        if ($request->hasFile('image')) {
            $this->validateUploadedImage($request->file('image'));
            $path = $request->file('image')->store('bulletins', 'public');
            // Only allow relative paths under bulletins/
            if (! is_string($path) || ! str_starts_with($path, 'bulletins/')) {
                abort(422, 'Upload gambar gagal.');
            }
            $validated['image'] = $path;
        }

        $validated['user_id'] = $user->id;

        $bulletin = new Bulletin($validated);

        if ($user->isAdmin()) {
            $bulletin->forceFill([
                'approval_status' => 'approved',
                'approved_by' => $user->id,
                'approved_at' => now(),
            ]);
        } else {
            $bulletin->forceFill([
                'approval_status' => 'pending',
                'approved_by' => null,
                'approved_at' => null,
                'rejected_by' => null,
                'rejected_at' => null,
                'rejection_reason' => null,
                'published_at' => null,
            ]);
        }

        $bulletin->save();

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
            'content' => 'required|string|max:20000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'category_id' => 'required|exists:categories,id',
            'published_at' => 'nullable|date',
        ]);

        $validated['content'] = strip_tags($validated['content']);

        if ($request->hasFile('image')) {
            if ($bulletin->image && str_starts_with((string) $bulletin->image, 'bulletins/')) {
                Storage::disk('public')->delete($bulletin->image);
            }
            $this->validateUploadedImage($request->file('image'));
            $path = $request->file('image')->store('bulletins', 'public');
            if (! is_string($path) || ! str_starts_with($path, 'bulletins/')) {
                abort(422, 'Upload gambar gagal.');
            }
            $validated['image'] = $path;
        }

        $bulletin->fill($validated);

        if (! $user->isAdmin()) {
            $bulletin->forceFill([
                'approval_status' => 'pending',
                'approved_by' => null,
                'approved_at' => null,
                'rejected_by' => null,
                'rejected_at' => null,
                'rejection_reason' => null,
                'published_at' => null,
            ]);
        }

        $bulletin->save();

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

        $bulletin->forceFill([
            'approval_status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'rejected_by' => null,
            'rejected_at' => null,
            'rejection_reason' => null,
            'published_at' => $bulletin->published_at ?? now(),
        ])->save();

        return redirect()->route('bulletins.index')
            ->with('success', 'Buletin berhasil di-ACC dan dipublikasikan.');
    }

    public function reject(Bulletin $bulletin)
    {
        if ($bulletin->approval_status !== 'pending') {
            return redirect()->route('bulletins.index')
                ->with('success', 'Buletin sudah diproses sebelumnya.');
        }

        $bulletin->forceFill([
            'approval_status' => 'rejected',
            'rejected_by' => Auth::id(),
            'rejected_at' => now(),
            'approved_by' => null,
            'approved_at' => null,
            'published_at' => null,
        ])->save();

        return redirect()->route('bulletins.index')
            ->with('success', 'Buletin ditolak.');
    }

    private function ensureCanManage(Bulletin $bulletin): void {
        $user = Auth::user();
        if ($user->isAdmin()) { return; }
        abort_if((int) $bulletin->user_id !== (int) $user->id, 403);
    }

    /**
     * Validasi gambar upload: MIME akurat + magic bytes + ekstensi (defense-in-depth).
     */
    private function validateUploadedImage(\Illuminate\Http\UploadedFile $file): void {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file->getRealPath());
        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (! in_array($mime, $allowedMimes, true)) {
            abort(422, 'Format gambar tidak didukung.');
        }
        $handle = fopen($file->getRealPath(), 'rb');
        $header = fread($handle, 12);
        fclose($handle);
        $validHeaders = ["\xFF\xD8\xFF", "\x89PNG\r\n\x1A\n", 'GIF8', 'RIFF'];
        $ok = false;
        foreach ($validHeaders as $exp) {
            if (str_starts_with($header, $exp)) { $ok = true; break; }
        }
        if (! $ok) { abort(422, 'File gambar tidak valid.'); }
        $ext = strtolower($file->getClientOriginalExtension());
        if (! in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
            abort(422, 'Ekstensi file tidak diizinkan.');
        }
    }
}
