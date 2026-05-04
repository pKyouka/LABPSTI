<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Bulletin;
use App\Models\LabUsageRecord;
use App\Models\Category;
use App\Models\InventoryItem;

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

        $inventoryItems = InventoryItem::where('is_active', true)
            ->where('available_stock', '>', 0)
            ->orderBy('name')
            ->get();

        return view('welcome', compact('bulletins', 'categories', 'stats', 'usagePerLab', 'inventoryItems'));
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

    public function inventory()
    {
        $inventoryQuery = InventoryItem::query()
            ->where('is_active', true)
            ->where('available_stock', '>', 0);

        $summary = [
            'items_count' => (clone $inventoryQuery)->count(),
            'available_stock_total' => (clone $inventoryQuery)->sum('available_stock'),
            'overall_stock_total' => (clone $inventoryQuery)->sum('total_stock'),
            'categories_count' => (clone $inventoryQuery)->whereNotNull('category')->distinct('category')->count('category'),
        ];

        $inventoryItems = $inventoryQuery
            ->orderByDesc('available_stock')
            ->orderBy('name')
            ->paginate(12);

        return view('inventory.public-index', compact('inventoryItems', 'summary'));
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
