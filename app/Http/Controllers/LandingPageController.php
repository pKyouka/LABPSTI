<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Bulletin;
use App\Models\LabUsageRecord;
use App\Models\Category;

class LandingPageController extends Controller
{
    public function index()
    {
        $bulletins = Bulletin::with('category')
            ->where('approval_status', 'approved')
            ->latest()
            ->take(6)
            ->get();
        $categories = Category::all()->unique('name');
        $stats = [
            'total_usage' => LabUsageRecord::count(),
            'total_bulletins' => Bulletin::where('approval_status', 'approved')->count(),
        ];

        // Chart data for landing page
        $usagePerLab = LabUsageRecord::select('category_id', \Illuminate\Support\Facades\DB::raw('count(*) as count'))
            ->groupBy('category_id')
            ->with('category')
            ->get()
            ->map(function ($item) {
                return [
                    'label' => $item->category->name,
                    'count' => $item->count,
                ];
            });

        return view('welcome', compact('bulletins', 'categories', 'stats', 'usagePerLab'));
    }

    public function bulletins()
    {
        $bulletins = Bulletin::with('category')
            ->where('approval_status', 'approved')
            ->latest('published_at')
            ->latest('created_at')
            ->paginate(9);

        return view('bulletins.public-index', compact('bulletins'));
    }

    public function showBulletin(Bulletin $bulletin)
    {
        abort_if($bulletin->approval_status !== 'approved', 404);

        $bulletin->load('category');

        $relatedBulletins = Bulletin::with('category')
            ->where('id', '!=', $bulletin->id)
            ->where('category_id', $bulletin->category_id)
            ->where('approval_status', 'approved')
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('bulletins.public-show', compact('bulletin', 'relatedBulletins'));
    }
}
