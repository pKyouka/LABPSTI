<?php

namespace App\Http\Controllers;

use App\Models\BorrowRecord;
use App\Models\LabUsageRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (!$user || !$user->isAdmin()) {
            abort(403);
        }

        $today = Carbon::today();
        $weekStart = Carbon::today()->startOfWeek();
        $monthStart = Carbon::today()->startOfMonth();

        // Borrow records stats
        $borrowToday = BorrowRecord::whereDate('created_at', $today)->count();
        $borrowWeek = BorrowRecord::whereBetween('created_at', [$weekStart, Carbon::today()->endOfDay()])->count();
        $borrowMonth = BorrowRecord::whereBetween('created_at', [$monthStart, Carbon::today()->endOfMonth()])->count();

        // Lab usage records stats
        $usageToday = LabUsageRecord::whereDate('created_at', $today)->count();
        $usageWeek = LabUsageRecord::whereBetween('created_at', [$weekStart, Carbon::today()->endOfDay()])->count();
        $usageMonth = LabUsageRecord::whereBetween('created_at', [$monthStart, Carbon::today()->endOfMonth()])->count();

        return view('reports.index', compact(
            'borrowToday', 'borrowWeek', 'borrowMonth',
            'usageToday', 'usageWeek', 'usageMonth'
        ));
    }

    public function borrowDaily()
    {
        $user = Auth::user();

        if (!$user || !$user->isAdmin()) {
            abort(403);
        }

        $today = Carbon::today();

        $records = BorrowRecord::whereDate('created_at', $today)
            ->with('inventoryItem')
            ->get()
            ->groupBy('inventory_item_id')
            ->map(function ($items) {
                return [
                    'item' => $items->first()->inventoryItem,
                    'count' => $items->count(),
                    'quantity_total' => $items->sum('quantity'),
                    'records' => $items,
                ];
            });

        return view('reports.borrow-daily', compact('records'));
    }

    public function borrowWeekly()
    {
        $user = Auth::user();

        if (!$user || !$user->isAdmin()) {
            abort(403);
        }

        $weekStart = Carbon::today()->startOfWeek();
        $weekEnd = Carbon::today()->endOfWeek();

        $records = BorrowRecord::whereBetween('created_at', [$weekStart, $weekEnd])
            ->with('inventoryItem')
            ->get()
            ->groupBy(function ($item) {
                return $item->created_at->format('Y-m-d');
            });

        $dailyStats = [];
        for ($date = clone $weekStart; $date <= $weekEnd; $date->addDay()) {
            $dateStr = $date->format('Y-m-d');
            $dailyStats[$dateStr] = [
                'date' => $date->copy(),
                'count' => $records->get($dateStr)?->count() ?? 0,
                'quantity' => $records->get($dateStr)?->sum('quantity') ?? 0,
            ];
        }

        return view('reports.borrow-weekly', compact('records', 'dailyStats', 'weekStart', 'weekEnd'));
    }

    public function borrowMonthly()
    {
        $user = Auth::user();

        if (!$user || !$user->isAdmin()) {
            abort(403);
        }

        $selectedMonth = request('month', Carbon::now()->format('m'));
        $selectedYear = request('year', Carbon::now()->format('Y'));

        $monthStart = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();

        $records = BorrowRecord::whereBetween('created_at', [$monthStart, $monthEnd])
            ->with('inventoryItem')
            ->get()
            ->groupBy(function ($item) {
                return $item->created_at->format('Y-m-d');
            });

        $dailyStats = [];
        for ($date = clone $monthStart; $date <= $monthEnd; $date->addDay()) {
            $dateStr = $date->format('Y-m-d');
            $dailyStats[$dateStr] = [
                'date' => $date->copy(),
                'count' => $records->get($dateStr)?->count() ?? 0,
                'quantity' => $records->get($dateStr)?->sum('quantity') ?? 0,
            ];
        }

        // Weekly summary
        $weeklyStats = [];
        $currentWeekStart = clone $monthStart;
        while ($currentWeekStart <= $monthEnd) {
            $currentWeekEnd = $currentWeekStart->copy()->addDays(6);
            if ($currentWeekEnd > $monthEnd) {
                $currentWeekEnd = $monthEnd;
            }

            $weekKey = $currentWeekStart->format('Y-m-d');
            $weekRecords = BorrowRecord::whereBetween('created_at', [$currentWeekStart, $currentWeekEnd->endOfDay()])->get();

            $weeklyStats[$weekKey] = [
                'start' => $currentWeekStart->copy(),
                'end' => $currentWeekEnd->copy(),
                'count' => $weekRecords->count(),
                'quantity' => $weekRecords->sum('quantity'),
            ];

            $currentWeekStart->addDays(7);
        }

        return view('reports.borrow-monthly', compact('records', 'dailyStats', 'weeklyStats', 'monthStart', 'monthEnd', 'selectedMonth', 'selectedYear'));
    }

    public function borrowMonthlyPdf()
    {
        $user = Auth::user();

        if (!$user || !$user->isAdmin()) {
            abort(403);
        }

        $selectedMonth = request('month', Carbon::now()->format('m'));
        $selectedYear = request('year', Carbon::now()->format('Y'));

        $monthStart = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();

        $records = BorrowRecord::whereBetween('created_at', [$monthStart, $monthEnd])
            ->with('inventoryItem')
            ->get()
            ->groupBy(function ($item) {
                return $item->created_at->format('Y-m-d');
            });

        $dailyStats = [];
        for ($date = clone $monthStart; $date <= $monthEnd; $date->addDay()) {
            $dateStr = $date->format('Y-m-d');
            $dailyStats[$dateStr] = [
                'date' => $date->copy(),
                'count' => $records->get($dateStr)?->count() ?? 0,
                'quantity' => $records->get($dateStr)?->sum('quantity') ?? 0,
            ];
        }

        $weeklyStats = [];
        $currentWeekStart = clone $monthStart;
        while ($currentWeekStart <= $monthEnd) {
            $currentWeekEnd = $currentWeekStart->copy()->addDays(6);
            if ($currentWeekEnd > $monthEnd) {
                $currentWeekEnd = $monthEnd;
            }

            $weekKey = $currentWeekStart->format('Y-m-d');
            $weekRecords = BorrowRecord::whereBetween('created_at', [$currentWeekStart, $currentWeekEnd->endOfDay()])->get();

            $weeklyStats[$weekKey] = [
                'start' => $currentWeekStart->copy(),
                'end' => $currentWeekEnd->copy(),
                'count' => $weekRecords->count(),
                'quantity' => $weekRecords->sum('quantity'),
            ];

            $currentWeekStart->addDays(7);
        }

        $totalRecords = $records->flatten()->count();
        $totalQuantity = $records->flatten()->sum('quantity');

        $pdf = Pdf::loadView('reports.pdf.borrow-monthly', compact('records', 'dailyStats', 'weeklyStats', 'monthStart', 'monthEnd', 'selectedMonth', 'selectedYear', 'totalRecords', 'totalQuantity'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("laporan-peminjaman-{$selectedYear}-{$selectedMonth}.pdf");
    }

    public function usageDaily()
    {
        $user = Auth::user();

        if (!$user || !$user->isAdmin()) {
            abort(403);
        }

        $today = Carbon::today();

        $records = LabUsageRecord::whereDate('created_at', $today)
            ->with('category')
            ->get()
            ->groupBy('category_id')
            ->map(function ($items) {
                return [
                    'category' => $items->first()->category,
                    'count' => $items->count(),
                    'records' => $items,
                ];
            });

        return view('reports.usage-daily', compact('records'));
    }

    public function usageWeekly()
    {
        $user = Auth::user();

        if (!$user || !$user->isAdmin()) {
            abort(403);
        }

        $weekStart = Carbon::today()->startOfWeek();
        $weekEnd = Carbon::today()->endOfWeek();

        $records = LabUsageRecord::whereBetween('created_at', [$weekStart, $weekEnd])
            ->with('category')
            ->get()
            ->groupBy(function ($item) {
                return $item->created_at->format('Y-m-d');
            });

        $dailyStats = [];
        for ($date = clone $weekStart; $date <= $weekEnd; $date->addDay()) {
            $dateStr = $date->format('Y-m-d');
            $dailyStats[$dateStr] = [
                'date' => $date->copy(),
                'count' => $records->get($dateStr)?->count() ?? 0,
            ];
        }

        return view('reports.usage-weekly', compact('records', 'dailyStats', 'weekStart', 'weekEnd'));
    }

    public function usageMonthly()
    {
        $user = Auth::user();

        if (!$user || !$user->isAdmin()) {
            abort(403);
        }

        $selectedMonth = request('month', Carbon::now()->format('m'));
        $selectedYear = request('year', Carbon::now()->format('Y'));

        $monthStart = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();

        $records = LabUsageRecord::whereBetween('created_at', [$monthStart, $monthEnd])
            ->with('category')
            ->get()
            ->groupBy(function ($item) {
                return $item->created_at->format('Y-m-d');
            });

        $dailyStats = [];
        for ($date = clone $monthStart; $date <= $monthEnd; $date->addDay()) {
            $dateStr = $date->format('Y-m-d');
            $dailyStats[$dateStr] = [
                'date' => $date->copy(),
                'count' => $records->get($dateStr)?->count() ?? 0,
            ];
        }

        // Weekly summary
        $weeklyStats = [];
        $currentWeekStart = clone $monthStart;
        while ($currentWeekStart <= $monthEnd) {
            $currentWeekEnd = $currentWeekStart->copy()->addDays(6);
            if ($currentWeekEnd > $monthEnd) {
                $currentWeekEnd = $monthEnd;
            }

            $weekKey = $currentWeekStart->format('Y-m-d');
            $weekRecords = LabUsageRecord::whereBetween('created_at', [$currentWeekStart, $currentWeekEnd->endOfDay()])->get();

            $weeklyStats[$weekKey] = [
                'start' => $currentWeekStart->copy(),
                'end' => $currentWeekEnd->copy(),
                'count' => $weekRecords->count(),
            ];

            $currentWeekStart->addDays(7);
        }

        return view('reports.usage-monthly', compact('records', 'dailyStats', 'weeklyStats', 'monthStart', 'monthEnd', 'selectedMonth', 'selectedYear'));
    }

    public function usageMonthlyPdf()
    {
        $user = Auth::user();

        if (!$user || !$user->isAdmin()) {
            abort(403);
        }

        $selectedMonth = request('month', Carbon::now()->format('m'));
        $selectedYear = request('year', Carbon::now()->format('Y'));

        $monthStart = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();

        $records = LabUsageRecord::whereBetween('created_at', [$monthStart, $monthEnd])
            ->with('category')
            ->get()
            ->groupBy(function ($item) {
                return $item->created_at->format('Y-m-d');
            });

        $dailyStats = [];
        for ($date = clone $monthStart; $date <= $monthEnd; $date->addDay()) {
            $dateStr = $date->format('Y-m-d');
            $dailyStats[$dateStr] = [
                'date' => $date->copy(),
                'count' => $records->get($dateStr)?->count() ?? 0,
            ];
        }

        $weeklyStats = [];
        $currentWeekStart = clone $monthStart;
        while ($currentWeekStart <= $monthEnd) {
            $currentWeekEnd = $currentWeekStart->copy()->addDays(6);
            if ($currentWeekEnd > $monthEnd) {
                $currentWeekEnd = $monthEnd;
            }

            $weekKey = $currentWeekStart->format('Y-m-d');
            $weekRecords = LabUsageRecord::whereBetween('created_at', [$currentWeekStart, $currentWeekEnd->endOfDay()])->get();

            $weeklyStats[$weekKey] = [
                'start' => $currentWeekStart->copy(),
                'end' => $currentWeekEnd->copy(),
                'count' => $weekRecords->count(),
            ];

            $currentWeekStart->addDays(7);
        }

        $totalRecords = $records->flatten()->count();

        $pdf = Pdf::loadView('reports.pdf.usage-monthly', compact('records', 'dailyStats', 'weeklyStats', 'monthStart', 'monthEnd', 'selectedMonth', 'selectedYear', 'totalRecords'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("laporan-penggunaan-lab-{$selectedYear}-{$selectedMonth}.pdf");
    }
}
