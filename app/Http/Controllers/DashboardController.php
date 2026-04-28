<?php

namespace App\Http\Controllers;

use App\Models\Bulletin;
use App\Models\LabUsageRecord;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $isAdmin = $user?->isAdmin() ?? false;
        $nim = $user?->externalNim();

        $usageQuery = LabUsageRecord::query();

        if (! $isAdmin && $nim !== null) {
            $usageQuery->where('user_id_number', $nim);
        }

        $stats = [
            'bulletin_count' => Bulletin::count(),
            'usage_count' => (clone $usageQuery)->count(),
            'categories' => Category::count(),
        ];

        $usagePerLab = $usageQuery
            ->select('category_id', DB::raw('count(*) as count'))
            ->groupBy('category_id')
            ->with('category')
            ->get()
            ->map(function ($item) {
                return [
                    'label' => $item->category->name,
                    'count' => $item->count,
                ];
            });

        $dashboardTitle = $isAdmin ? 'Dashboard Admin' : 'Dashboard Mahasiswa';
        $chartTitle = $isAdmin ? 'Statistik Penggunaan per Lab' : 'Infografis Penggunaan Lab Anda';

        return view('dashboard', compact('stats', 'usagePerLab', 'dashboardTitle', 'chartTitle', 'isAdmin'));
    }
}
