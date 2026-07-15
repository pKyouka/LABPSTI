<?php

namespace App\Http\Controllers;

use App\Models\BorrowRecord;
use App\Models\LabUsageRecord;
use App\Models\VmBorrowRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\CustomExport;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ReportController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $weekStart = Carbon::today()->startOfWeek();
        $monthStart = Carbon::today()->startOfMonth();

        $borrowToday = BorrowRecord::whereDate('borrowed_at', $today)->count();
        $borrowWeek = BorrowRecord::whereBetween('borrowed_at', [$weekStart, Carbon::today()->endOfDay()])->count();
        $borrowMonth = BorrowRecord::whereBetween('borrowed_at', [$monthStart, Carbon::today()->endOfMonth()])->count();

        $usageToday = LabUsageRecord::whereDate('start_time', $today)->count();
        $usageWeek = LabUsageRecord::whereBetween('start_time', [$weekStart, Carbon::today()->endOfDay()])->count();
        $usageMonth = LabUsageRecord::whereBetween('start_time', [$monthStart, Carbon::today()->endOfMonth()])->count();

        $vmToday = VmBorrowRequest::whereDate('created_at', $today)->where('status', 'approved')->count();
        $vmWeek = VmBorrowRequest::whereBetween('created_at', [$weekStart, Carbon::today()->endOfDay()])->where('status', 'approved')->count();
        $vmMonth = VmBorrowRequest::whereBetween('created_at', [$monthStart, Carbon::today()->endOfMonth()])->where('status', 'approved')->count();

        $vmActive = VmBorrowRequest::where('status', 'approved')
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', Carbon::today());
            })
            ->count();

        $vmExpiringSoon = VmBorrowRequest::where('status', 'approved')
            ->whereNotNull('end_date')
            ->whereBetween('end_date', [Carbon::today(), Carbon::today()->addDays(7)])
            ->count();

        $vmExpired = VmBorrowRequest::where('status', 'approved')
            ->whereNotNull('end_date')
            ->where('end_date', '<', Carbon::today())
            ->count();

        return view('reports.index', compact(
            'borrowToday', 'borrowWeek', 'borrowMonth',
            'usageToday', 'usageWeek', 'usageMonth',
            'vmToday', 'vmWeek', 'vmMonth',
            'vmActive', 'vmExpiringSoon', 'vmExpired'
        ));
    }

    public function borrowDaily()
    {
        $today = Carbon::today();

        $records = BorrowRecord::whereDate('borrowed_at', $today)
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
        $weekStart = Carbon::today()->startOfWeek();
        $weekEnd = Carbon::today()->endOfWeek();

        $records = BorrowRecord::whereBetween('borrowed_at', [$weekStart, $weekEnd])
            ->with('inventoryItem')
            ->get()
            ->groupBy(function ($item) {
                return $item->borrowed_at->format('Y-m-d');
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
        $selectedMonth = (int) request('month', Carbon::now()->month);
        $selectedYear = (int) request('year', Carbon::now()->year);

        abort_if($selectedMonth < 1 || $selectedMonth > 12, 422, 'Bulan tidak valid.');
        abort_if($selectedYear < 2020 || $selectedYear > Carbon::now()->year + 1, 422, 'Tahun tidak valid.');

        $monthStart = Carbon::createFromDate((int) $selectedYear, (int) $selectedMonth, 1)->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();

        $records = BorrowRecord::whereBetween('borrowed_at', [$monthStart, $monthEnd])
            ->with('inventoryItem')
            ->get()
            ->groupBy(function ($item) {
                return $item->borrowed_at->format('Y-m-d');
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
            $weekRecords = BorrowRecord::whereBetween('borrowed_at', [$currentWeekStart, $currentWeekEnd->endOfDay()])->get();

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
        $selectedMonth = (int) request('month', Carbon::now()->month);
        $selectedYear = (int) request('year', Carbon::now()->year);

        abort_if($selectedMonth < 1 || $selectedMonth > 12, 422, 'Bulan tidak valid.');
        abort_if($selectedYear < 2020 || $selectedYear > Carbon::now()->year + 1, 422, 'Tahun tidak valid.');

        $monthStart = Carbon::createFromDate((int) $selectedYear, (int) $selectedMonth, 1)->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();

        $records = BorrowRecord::whereBetween('borrowed_at', [$monthStart, $monthEnd])
            ->with('inventoryItem')
            ->get()
            ->groupBy(function ($item) {
                return $item->borrowed_at->format('Y-m-d');
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
            $weekRecords = BorrowRecord::whereBetween('borrowed_at', [$currentWeekStart, $currentWeekEnd->endOfDay()])->get();

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
        $today = Carbon::today();

        $records = LabUsageRecord::whereDate('start_time', $today)
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
        $weekStart = Carbon::today()->startOfWeek();
        $weekEnd = Carbon::today()->endOfWeek();

        $records = LabUsageRecord::whereBetween('start_time', [$weekStart, $weekEnd])
            ->with('category')
            ->get()
            ->groupBy(function ($item) {
                return $item->start_time->format('Y-m-d');
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
        $selectedMonth = request('month', Carbon::now()->format('m'));
        $selectedYear = request('year', Carbon::now()->format('Y'));

        $monthStart = Carbon::createFromDate((int) $selectedYear, (int) $selectedMonth, 1)->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();

        $records = LabUsageRecord::whereBetween('start_time', [$monthStart, $monthEnd])
            ->with('category')
            ->get()
            ->groupBy(function ($item) {
                return $item->start_time->format('Y-m-d');
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
            $weekRecords = LabUsageRecord::whereBetween('start_time', [$currentWeekStart, $currentWeekEnd->endOfDay()])->get();

            $weeklyStats[$weekKey] = [
                'start' => $currentWeekStart->copy(),
                'end' => $currentWeekEnd->copy(),
                'count' => $weekRecords->count(),
            ];

            $currentWeekStart->addDays(7);
        }

        return view('reports.usage-monthly', compact('records', 'dailyStats', 'weeklyStats', 'monthStart', 'monthEnd', 'selectedMonth', 'selectedYear'));
    }

    public function exportPage()
    {
        $defaultStart = Carbon::now()->startOfMonth()->format('Y-m-d');
        $defaultEnd = Carbon::now()->format('Y-m-d');

        return view('reports.export', compact('defaultStart', 'defaultEnd'));
    }

    // =========== VM Report Methods ===========

    public function vmAll()
    {
        $query = VmBorrowRequest::with('user');

        if ($status = request('status')) {
            $query->where('status', $status);
        }

        if ($os = request('os')) {
            $query->where('os_type', $os);
        }

        if ($expiry = request('expiry')) {
            if ($expiry === 'active') {
                $query->where('status', 'approved')
                    ->where(function ($q) {
                        $q->whereNull('end_date')->orWhere('end_date', '>=', Carbon::today());
                    });
            } elseif ($expiry === 'expiring_soon') {
                $query->where('status', 'approved')
                    ->whereNotNull('end_date')
                    ->whereBetween('end_date', [Carbon::today(), Carbon::today()->addDays(7)]);
            } elseif ($expiry === 'expired') {
                $query->where('status', 'approved')
                    ->whereNotNull('end_date')
                    ->where('end_date', '<', Carbon::today());
            }
        }

        if ($search = request('search')) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('id_number', 'like', "%{$search}%");
            });
        }

        $vms = $query->latest()->paginate(20);

        $total = VmBorrowRequest::count();
        $active = VmBorrowRequest::where('status', 'approved')
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', Carbon::today());
            })->count();
        $expiringSoon = VmBorrowRequest::where('status', 'approved')
            ->whereNotNull('end_date')
            ->whereBetween('end_date', [Carbon::today(), Carbon::today()->addDays(7)])
            ->count();
        $expired = VmBorrowRequest::where('status', 'approved')
            ->whereNotNull('end_date')
            ->where('end_date', '<', Carbon::today())
            ->count();
        $pending = VmBorrowRequest::whereIn('status', ['pending', 'on-review'])->count();

        $summary = compact('total', 'active', 'expiringSoon', 'expired', 'pending');

        $allVms = VmBorrowRequest::all();
        $osStats = [];
        foreach (['debian', 'ubuntu'] as $os) {
            $totalByOs = $allVms->where('os_type', $os);
            $osStats[$os] = [
                'total' => $totalByOs->count(),
                'active' => $totalByOs->where('status', 'approved')
                    ->filter(function ($vm) {
                        return $vm->end_date === null || $vm->end_date >= Carbon::today();
                    })->count(),
                'expired' => $totalByOs->where('status', 'approved')
                    ->filter(function ($vm) {
                        return $vm->end_date !== null && $vm->end_date < Carbon::today();
                    })->count(),
            ];
        }

        $expiringSoonList = VmBorrowRequest::where('status', 'approved')
            ->whereNotNull('end_date')
            ->whereBetween('end_date', [Carbon::today(), Carbon::today()->addDays(7)])
            ->with('user')
            ->orderBy('end_date')
            ->get();

        return view('reports.vm-all', compact(
            'vms', 'summary', 'osStats', 'expiringSoonList'
        ));
    }

    public function vmDaily()
    {
        $today = Carbon::today();

        $records = VmBorrowRequest::whereDate('created_at', $today)
            ->with('user')
            ->get()
            ->groupBy('status')
            ->map(function ($items) {
                return [
                    'count' => $items->count(),
                    'records' => $items,
                ];
            });

        $activeVms = VmBorrowRequest::where('status', 'approved')
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', Carbon::today());
            })
            ->with('user')
            ->get();

        return view('reports.vm-daily', compact('records', 'activeVms'));
    }

    public function vmWeekly()
    {
        $weekStart = Carbon::today()->startOfWeek();
        $weekEnd = Carbon::today()->endOfWeek();

        $records = VmBorrowRequest::whereBetween('created_at', [$weekStart, $weekEnd])
            ->with('user')
            ->get()
            ->groupBy(function ($item) {
                return $item->created_at->format('Y-m-d');
            });

        $dailyStats = [];
        for ($date = clone $weekStart; $date <= $weekEnd; $date->addDay()) {
            $dateStr = $date->format('Y-m-d');
            $dayRequests = $records->get($dateStr);
            $dailyStats[$dateStr] = [
                'date' => $date->copy(),
                'total' => $dayRequests?->count() ?? 0,
                'approved' => $dayRequests?->where('status', 'approved')->count() ?? 0,
                'pending' => $dayRequests?->whereIn('status', ['pending', 'on-review'])->count() ?? 0,
                'rejected' => $dayRequests?->where('status', 'rejected')->count() ?? 0,
            ];
        }

        return view('reports.vm-weekly', compact('records', 'dailyStats', 'weekStart', 'weekEnd'));
    }

    public function vmMonthly()
    {
        $selectedMonth = (int) request('month', Carbon::now()->month);
        $selectedYear = (int) request('year', Carbon::now()->year);

        abort_if($selectedMonth < 1 || $selectedMonth > 12, 422, 'Bulan tidak valid.');
        abort_if($selectedYear < 2020 || $selectedYear > Carbon::now()->year + 1, 422, 'Tahun tidak valid.');

        $monthStart = Carbon::createFromDate((int) $selectedYear, (int) $selectedMonth, 1)->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();

        $records = VmBorrowRequest::whereBetween('created_at', [$monthStart, $monthEnd])
            ->with('user')
            ->get()
            ->groupBy(function ($item) {
                return $item->created_at->format('Y-m-d');
            });

        $dailyStats = [];
        for ($date = clone $monthStart; $date <= $monthEnd; $date->addDay()) {
            $dateStr = $date->format('Y-m-d');
            $dayRequests = $records->get($dateStr);
            $dailyStats[$dateStr] = [
                'date' => $date->copy(),
                'total' => $dayRequests?->count() ?? 0,
                'approved' => $dayRequests?->where('status', 'approved')->count() ?? 0,
                'pending' => $dayRequests?->whereIn('status', ['pending', 'on-review'])->count() ?? 0,
                'rejected' => $dayRequests?->where('status', 'rejected')->count() ?? 0,
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
            $weekRecords = VmBorrowRequest::whereBetween('created_at', [$currentWeekStart, $currentWeekEnd->endOfDay()])->get();

            $weeklyStats[$weekKey] = [
                'start' => $currentWeekStart->copy(),
                'end' => $currentWeekEnd->copy(),
                'total' => $weekRecords->count(),
                'approved' => $weekRecords->where('status', 'approved')->count(),
                'pending' => $weekRecords->whereIn('status', ['pending', 'on-review'])->count(),
                'rejected' => $weekRecords->where('status', 'rejected')->count(),
            ];

            $currentWeekStart->addDays(7);
        }

        $vmsCreatedThisMonth = VmBorrowRequest::whereBetween('created_at', [$monthStart, $monthEnd])
            ->where('status', 'approved')
            ->count();

        $vmsActiveEndOfMonth = VmBorrowRequest::where('status', 'approved')
            ->where(function ($q) use ($monthEnd) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $monthEnd);
            })
            ->count();

        $vmsExpiredThisMonth = VmBorrowRequest::where('status', 'approved')
            ->whereNotNull('end_date')
            ->whereBetween('end_date', [$monthStart, $monthEnd])
            ->count();

        return view('reports.vm-monthly', compact(
            'records', 'dailyStats', 'weeklyStats',
            'monthStart', 'monthEnd',
            'selectedMonth', 'selectedYear',
            'vmsCreatedThisMonth', 'vmsActiveEndOfMonth', 'vmsExpiredThisMonth'
        ));
    }

    public function vmMonthlyPdf()
    {
        $selectedMonth = (int) request('month', Carbon::now()->month);
        $selectedYear = (int) request('year', Carbon::now()->year);

        abort_if($selectedMonth < 1 || $selectedMonth > 12, 422, 'Bulan tidak valid.');
        abort_if($selectedYear < 2020 || $selectedYear > Carbon::now()->year + 1, 422, 'Tahun tidak valid.');

        $monthStart = Carbon::createFromDate((int) $selectedYear, (int) $selectedMonth, 1)->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();

        $records = VmBorrowRequest::whereBetween('created_at', [$monthStart, $monthEnd])
            ->with('user')
            ->get()
            ->groupBy(function ($item) {
                return $item->created_at->format('Y-m-d');
            });

        $dailyStats = [];
        for ($date = clone $monthStart; $date <= $monthEnd; $date->addDay()) {
            $dateStr = $date->format('Y-m-d');
            $dayRequests = $records->get($dateStr);
            $dailyStats[$dateStr] = [
                'date' => $date->copy(),
                'total' => $dayRequests?->count() ?? 0,
                'approved' => $dayRequests?->where('status', 'approved')->count() ?? 0,
                'pending' => $dayRequests?->whereIn('status', ['pending', 'on-review'])->count() ?? 0,
                'rejected' => $dayRequests?->where('status', 'rejected')->count() ?? 0,
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
            $weekRecords = VmBorrowRequest::whereBetween('created_at', [$currentWeekStart, $currentWeekEnd->endOfDay()])->get();

            $weeklyStats[$weekKey] = [
                'start' => $currentWeekStart->copy(),
                'end' => $currentWeekEnd->copy(),
                'total' => $weekRecords->count(),
                'approved' => $weekRecords->where('status', 'approved')->count(),
                'pending' => $weekRecords->whereIn('status', ['pending', 'on-review'])->count(),
                'rejected' => $weekRecords->where('status', 'rejected')->count(),
            ];

            $currentWeekStart->addDays(7);
        }

        $totalRecords = $records->flatten()->count();
        $totalApproved = $records->flatten()->where('status', 'approved')->count();
        $totalRejected = $records->flatten()->where('status', 'rejected')->count();
        $totalPending = $records->flatten()->whereIn('status', ['pending', 'on-review'])->count();

        $vmsCreatedThisMonth = VmBorrowRequest::whereBetween('created_at', [$monthStart, $monthEnd])
            ->where('status', 'approved')
            ->count();

        $vmsActiveEndOfMonth = VmBorrowRequest::where('status', 'approved')
            ->where(function ($q) use ($monthEnd) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $monthEnd);
            })
            ->count();

        $vmsExpiredThisMonth = VmBorrowRequest::where('status', 'approved')
            ->whereNotNull('end_date')
            ->whereBetween('end_date', [$monthStart, $monthEnd])
            ->count();

        $pdf = Pdf::loadView('reports.pdf.vm-monthly', compact(
            'records', 'dailyStats', 'weeklyStats',
            'monthStart', 'monthEnd',
            'selectedMonth', 'selectedYear',
            'totalRecords', 'totalApproved', 'totalRejected', 'totalPending',
            'vmsCreatedThisMonth', 'vmsActiveEndOfMonth', 'vmsExpiredThisMonth'
        ))->setPaper('a4', 'portrait');

        return $pdf->download("laporan-vm-{$selectedYear}-{$selectedMonth}.pdf");
    }

    public function usageExportExcel(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        $records = LabUsageRecord::whereBetween('start_time', [$start, $end])
            ->with('category')
            ->orderBy('start_time')
            ->get();

        $export = new CustomExport();
        $spreadsheet = (new \ReflectionClass($export))->getProperty('spreadsheet')->getValue($export);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Penggunaan Lab');

        $sheet->mergeCells('A1:H1');
        $sheet->setCellValue('A1', 'LAPORAN PENGGUNAAN LAB');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A2:H2');
        $sheet->setCellValue('A2', "Periode: {$start->format('d/m/Y')} - {$end->format('d/m/Y')}");
        $sheet->getStyle('A2')->getFont()->setSize(11);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $headers = ['No', 'Tanggal', 'Lab', 'Nama Pengguna', 'NIM/NIP', 'Waktu Mulai', 'Waktu Selesai', 'Keterangan'];
        $headerRow = 4;
        $col = 1;
        foreach ($headers as $header) {
            $cell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $headerRow;
            $sheet->setCellValue($cell, $header);
            $sheet->getStyle($cell)->getFont()->setBold(true)->setSize(10);
            $sheet->getStyle($cell)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFD9E2F3');
            $sheet->getStyle($cell)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $col++;
        }

        $row = 5;
        $no = 1;
        foreach ($records as $record) {
            $data = [
                $no++,
                $record->start_time?->format('d/m/Y') ?? '-',
                $record->category?->name ?? '-',
                $record->user_name ?? '-',
                $record->user_id_number ?? '-',
                $record->start_time?->format('H:i') ?? '-',
                $record->end_time?->format('H:i') ?? '-',
                $record->purpose ?? '-',
            ];

            $col = 1;
            foreach ($data as $value) {
                $cell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $row;
                $sheet->setCellValue($cell, $value);
                $sheet->getStyle($cell)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $col++;
            }
            $row++;
        }

        foreach (range(1, 8) as $c) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = "laporan-penggunaan-lab-{$startDate}-{$endDate}.xlsx";

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function borrowExportExcel(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        $records = BorrowRecord::whereBetween('borrowed_at', [$start, $end])
            ->with('inventoryItem')
            ->orderBy('borrowed_at')
            ->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Peminjaman Alat');

        $sheet->mergeCells('A1:J1');
        $sheet->setCellValue('A1', 'LAPORAN PEMINJAMAN ALAT & BARANG');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A2:J2');
        $sheet->setCellValue('A2', "Periode: {$start->format('d/m/Y')} - {$end->format('d/m/Y')}");
        $sheet->getStyle('A2')->getFont()->setSize(11);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $headers = ['No', 'Tanggal Pinjam', 'Nama Peminjam', 'NIM/NIP', 'Alat/Barang', 'Jumlah', 'Tenggat Waktu', 'Tanggal Kembali', 'Status', 'Keterangan'];
        $headerRow = 4;
        $col = 1;
        foreach ($headers as $header) {
            $cell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $headerRow;
            $sheet->setCellValue($cell, $header);
            $sheet->getStyle($cell)->getFont()->setBold(true)->setSize(10);
            $sheet->getStyle($cell)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFD9E2F3');
            $sheet->getStyle($cell)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $col++;
        }

        $row = 5;
        $no = 1;
        foreach ($records as $record) {
            $status = $record->status ?? ($record->returned_at ? 'Dikembalikan' : 'Dipinjam');
            $data = [
                $no++,
                $record->borrowed_at?->format('d/m/Y') ?? '-',
                $record->user_name ?? '-',
                $record->user_id_number ?? '-',
                $record->inventoryItem?->name ?? '-',
                $record->quantity ?? 0,
                $record->due_at?->format('d/m/Y') ?? '-',
                $record->returned_at?->format('d/m/Y') ?? '-',
                $status,
                $record->purpose ?? '-',
            ];

            $col = 1;
            foreach ($data as $value) {
                $cell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $row;
                $sheet->setCellValue($cell, $value);
                $sheet->getStyle($cell)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $col++;
            }
            $row++;
        }

        foreach (range(1, 10) as $c) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = "laporan-peminjaman-{$startDate}-{$endDate}.xlsx";

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function usageMonthlyPdf()
    {
        $selectedMonth = request('month', Carbon::now()->format('m'));
        $selectedYear = request('year', Carbon::now()->format('Y'));

        $monthStart = Carbon::createFromDate((int) $selectedYear, (int) $selectedMonth, 1)->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();

        $records = LabUsageRecord::whereBetween('start_time', [$monthStart, $monthEnd])
            ->with('category')
            ->get()
            ->groupBy(function ($item) {
                return $item->start_time->format('Y-m-d');
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
            $weekRecords = LabUsageRecord::whereBetween('start_time', [$currentWeekStart, $currentWeekEnd->endOfDay()])->get();

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
