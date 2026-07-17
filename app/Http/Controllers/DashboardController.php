<?php

namespace App\Http\Controllers;

use App\Models\Bulletin;
use App\Models\BorrowRecord;
use App\Models\InventoryItem;
use App\Models\LabUsageRecord;
use App\Models\Category;
use App\Models\VmBorrowRequest;
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
        $borrowQuery = BorrowRecord::query();

        // Non-admin must never see global records.
        if (! $isAdmin) {
            if ($nim === null) {
                $usageQuery->whereRaw('1 = 0');
                $borrowQuery->whereRaw('1 = 0');
            } else {
                $usageQuery->where('user_id_number', $nim);
                $borrowQuery->where('user_id_number', $nim);
            }
        }

        $stats = [
            'bulletin_count' => $isAdmin
                ? Bulletin::count()
                : Bulletin::where('approval_status', 'approved')->count(),
            'usage_count' => (clone $usageQuery)->count(),
            'borrow_count' => (clone $borrowQuery)->count(),
            'item_count' => InventoryItem::where('is_active', true)->count(),
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

        $vmBorrowRequests = collect();
        if (! $isAdmin && $user !== null) {
            $vmBorrowRequests = VmBorrowRequest::where('user_id', $user->id)
                ->latest()
                ->take(5)
                ->get();
        }

        $dashboardTitle = $isAdmin ? 'Dashboard Admin' : 'Dashboard Mahasiswa';
        $chartTitle = $isAdmin ? 'Statistik Penggunaan per Lab' : 'Infografis Penggunaan Lab Anda';

        return view('dashboard', compact('stats', 'usagePerLab', 'dashboardTitle', 'chartTitle', 'isAdmin', 'vmBorrowRequests'));
    }
}
