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
});

require __DIR__.'/auth.php';
