<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BulletinController;
use App\Http\Controllers\BorrowRecordController;
use App\Http\Controllers\InventoryItemController;
use App\Http\Controllers\LabUsageRecordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\LabController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VmBorrowController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingPageController::class, 'index'])->name('landing');
Route::get('/inventaris', [LandingPageController::class, 'inventory'])->name('landing.inventory');
Route::get('/kabar-berita', [LandingPageController::class, 'bulletins'])->name('landing.bulletins');
Route::get('/kabar-berita/{bulletin}', [LandingPageController::class, 'showBulletin'])->name('landing.bulletins.show');
Route::get('/lab/{category}', [LabController::class, 'show'])->name('lab.show');
Route::get('/lab/{category}/berita/{bulletin}', [LabController::class, 'showBulletin'])->name('lab.bulletins.show');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Bulletin is available for all authenticated users, but scoped per role in controller.
    Route::get('bulletins', [BulletinController::class, 'index'])->name('bulletins.index');
    Route::get('bulletins/create', [BulletinController::class, 'create'])->name('bulletins.create');
    Route::post('bulletins', [BulletinController::class, 'store'])->name('bulletins.store');
    Route::get('bulletins/{bulletin}/edit', [BulletinController::class, 'edit'])->name('bulletins.edit');
    Route::put('bulletins/{bulletin}', [BulletinController::class, 'update'])->name('bulletins.update');
    Route::delete('bulletins/{bulletin}', [BulletinController::class, 'destroy'])->name('bulletins.destroy');
    Route::post('bulletins/{bulletin}/approve', [BulletinController::class, 'approve'])->name('bulletins.approve');
    Route::post('bulletins/{bulletin}/reject', [BulletinController::class, 'reject'])->name('bulletins.reject');

    // Usage record is available for all authenticated users, but scoped per role in controller.
    Route::resource('usage_records', LabUsageRecordController::class)->except(['show', 'edit', 'update']);

    Route::get('inventory_items', [InventoryItemController::class, 'index'])->name('inventory_items.index');
    Route::get('inventory_items/create', [InventoryItemController::class, 'create'])->name('inventory_items.create');
    Route::get('inventory_items/{inventoryItem}', [InventoryItemController::class, 'show'])->name('inventory_items.show');
    Route::post('inventory_items', [InventoryItemController::class, 'store'])->name('inventory_items.store');
    Route::delete('inventory_items/{inventoryItem}', [InventoryItemController::class, 'destroy'])->name('inventory_items.destroy');

    Route::get('borrow_records', [BorrowRecordController::class, 'index'])->name('borrow_records.index');
    Route::get('borrow_records/create', [BorrowRecordController::class, 'create'])->name('borrow_records.create');
    Route::post('borrow_records', [BorrowRecordController::class, 'store'])->name('borrow_records.store');
    Route::post('borrow_records/{borrowRecord}/approve', [BorrowRecordController::class, 'approve'])->name('borrow_records.approve');
    Route::post('borrow_records/{borrowRecord}/reject', [BorrowRecordController::class, 'reject'])->name('borrow_records.reject');
    Route::post('borrow_records/{borrowRecord}/return', [BorrowRecordController::class, 'markReturned'])->name('borrow_records.return');

    // VM Borrow
    Route::get('vm_borrow', [VmBorrowController::class, 'index'])->name('vm_borrow.index');
    Route::get('vm_borrow/create', [VmBorrowController::class, 'create'])->name('vm_borrow.create');
    Route::post('vm_borrow', [VmBorrowController::class, 'store'])->name('vm_borrow.store');
    Route::get('vm_borrow/{vmBorrow}', [VmBorrowController::class, 'show'])->name('vm_borrow.show');
    Route::post('vm_borrow/{vmBorrow}/approve', [VmBorrowController::class, 'approve'])->name('vm_borrow.approve');
    Route::post('vm_borrow/{vmBorrow}/reject', [VmBorrowController::class, 'reject'])->name('vm_borrow.reject');

    // User Management (Admin only)
    Route::resource('users', UserController::class);

    // Reports (Admin only)
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/borrow/daily', [ReportController::class, 'borrowDaily'])->name('reports.borrow.daily');
    Route::get('reports/borrow/weekly', [ReportController::class, 'borrowWeekly'])->name('reports.borrow.weekly');
    Route::get('reports/borrow/monthly', [ReportController::class, 'borrowMonthly'])->name('reports.borrow.monthly');
    Route::get('reports/borrow/monthly/pdf', [ReportController::class, 'borrowMonthlyPdf'])->name('reports.borrow.monthly.pdf');
    Route::get('reports/usage/daily', [ReportController::class, 'usageDaily'])->name('reports.usage.daily');
    Route::get('reports/usage/weekly', [ReportController::class, 'usageWeekly'])->name('reports.usage.weekly');
    Route::get('reports/usage/monthly', [ReportController::class, 'usageMonthly'])->name('reports.usage.monthly');
    Route::get('reports/usage/monthly/pdf', [ReportController::class, 'usageMonthlyPdf'])->name('reports.usage.monthly.pdf');
    Route::get('reports/export', [ReportController::class, 'exportPage'])->name('reports.export');
    Route::get('reports/usage/export', [ReportController::class, 'usageExportExcel'])->name('reports.usage.export');
    Route::get('reports/borrow/export', [ReportController::class, 'borrowExportExcel'])->name('reports.borrow.export');

        // VM Borrow Reset
        Route::post("vm_borrow/{vmBorrow}/request-reset", [VmBorrowController::class, "requestReset"])->name("vm_borrow.request_reset");
        Route::post("vm_borrow/{vmBorrow}/approve-reset", [VmBorrowController::class, "approveReset"])->name("vm_borrow.approve_reset");
        Route::post("vm_borrow/{vmBorrow}/reject-reset", [VmBorrowController::class, "rejectReset"])->name("vm_borrow.reject_reset");
	});

	require __DIR__.'/auth.php';
