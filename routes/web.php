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
use App\Http\Controllers\VmHostController;
use App\Http\Controllers\VmServiceRequestController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
| Accessible without authentication.
*/

Route::get('/', [LandingPageController::class, 'index'])->name('landing');
Route::get('/inventaris', [LandingPageController::class, 'inventory'])->name('landing.inventory');
Route::get('/kabar-berita', [LandingPageController::class, 'bulletins'])->name('landing.bulletins');
Route::get('/kabar-berita/{bulletin}', [LandingPageController::class, 'showBulletin'])->name('landing.bulletins.show');
Route::get('/lab/{category}', [LabController::class, 'show'])->name('lab.show');
Route::get('/lab/{category}/berita/{bulletin}', [LabController::class, 'showBulletin'])->name('lab.bulletins.show');

/*
|--------------------------------------------------------------------------
| Public VM Host Status
|--------------------------------------------------------------------------
| Accessible without login.
*/

Route::get('/vm-hosts', [VmHostController::class, 'publicIndex'])->name('vm_hosts.public');

/*
|--------------------------------------------------------------------------
| Authenticated Routes — Dashboard
|--------------------------------------------------------------------------
| Requires auth + verified email.
*/

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| Notifications (all authenticated users)
|--------------------------------------------------------------------------
*/

Route::post('notifications/{notification}/read', function (\App\Models\Notification $notification) {
    if ((int) $notification->notifiable_id !== (int) Auth::id()) {
        abort(403);
    }
    $notification->markAsRead();

    // Redirect back or to related resource
    $type = $notification->data['type'] ?? '';
    if (in_array($type, ['service_request_received', 'service_request_updated', 'vm_borrow_request_updated'])) {
        return redirect()->route('vm_borrow.show', $notification->data['vm_borrow_request_id']);
    }
    if (in_array($type, ['reset_request_received', 'reset_request_updated'])) {
        return redirect()->route('vm_borrow.show', $notification->data['vm_borrow_request_id']);
    }

    return redirect()->back();
})->name('notifications.read');

Route::post('notifications/mark-all-read', function () {
    DB::table('notifications')
        ->where('notifiable_id', Auth::id())
        ->whereNull('read_at')
        ->update(['read_at' => now()]);
    return redirect()->back();
})->name('notifications.markAllRead');

/*
|--------------------------------------------------------------------------
| Shared Routes (auth only — accessible by all authenticated users)
|--------------------------------------------------------------------------
| Role-based scoping handled inside each controller.
*/

Route::middleware('auth')->group(function () {

    // Profile — same for all users
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Bulletin — all can read; admin/auto-approve on create
    Route::get('bulletins', [BulletinController::class, 'index'])->name('bulletins.index');
    Route::get('bulletins/create', [BulletinController::class, 'create'])->name('bulletins.create');
    Route::post('bulletins', [BulletinController::class, 'store'])->name('bulletins.store');
    Route::get('bulletins/{bulletin}/edit', [BulletinController::class, 'edit'])->name('bulletins.edit');
    Route::put('bulletins/{bulletin}', [BulletinController::class, 'update'])->name('bulletins.update');
    Route::delete('bulletins/{bulletin}', [BulletinController::class, 'destroy'])->name('bulletins.destroy');

    // Lab Usage — scoped per role in controller
    Route::resource('usage_records', LabUsageRecordController::class)->except(['show', 'edit', 'update']);

    // Borrow Records — scoped per role in controller
    Route::get('borrow_records', [BorrowRecordController::class, 'index'])->name('borrow_records.index');
    Route::get('borrow_records/create', [BorrowRecordController::class, 'create'])->name('borrow_records.create');
    Route::post('borrow_records', [BorrowRecordController::class, 'store'])->name('borrow_records.store');
    Route::post('borrow_records/{borrowRecord}/approve', [BorrowRecordController::class, 'approve'])->name('borrow_records.approve');
    Route::post('borrow_records/{borrowRecord}/reject', [BorrowRecordController::class, 'reject'])->name('borrow_records.reject');
    Route::post('borrow_records/{borrowRecord}/return', [BorrowRecordController::class, 'markReturned'])->name('borrow_records.return');

    // Inventory — index/show open to all; create/store/destroy restricted below
    Route::get('inventory_items', [InventoryItemController::class, 'index'])->name('inventory_items.index');
    Route::get('inventory_items/{inventoryItem}', [InventoryItemController::class, 'show'])->name('inventory_items.show');

    // VM Borrow — index/show scoped per role
    Route::get('vm_borrow', [VmBorrowController::class, 'index'])->name('vm_borrow.index');
    Route::get('vm_borrow/create', [VmBorrowController::class, 'create'])->name('vm_borrow.create');
    Route::post('vm_borrow', [VmBorrowController::class, 'store'])->name('vm_borrow.store');
    Route::get('vm_borrow/{vmBorrow}', [VmBorrowController::class, 'show'])->name('vm_borrow.show');
    Route::post('vm_borrow/{vmBorrow}/move-to-review', [VmBorrowController::class, 'moveToReview'])->name('vm_borrow.moveToReview');
    Route::post('vm_borrow/{vmBorrow}/approve', [VmBorrowController::class, 'approve'])->name('vm_borrow.approve');
    Route::post('vm_borrow/{vmBorrow}/reject', [VmBorrowController::class, 'reject'])->name('vm_borrow.reject');

    // VM Borrow Reset — any owner can request; admin approves/rejects
    Route::post("vm_borrow/{vmBorrow}/request-reset", [VmBorrowController::class, "requestReset"])->name("vm_borrow.request_reset");
    Route::post("vm_borrow/{vmBorrow}/approve-reset", [VmBorrowController::class, "approveReset"])->name("vm_borrow.approve_reset");
    Route::post("vm_borrow/{vmBorrow}/reject-reset", [VmBorrowController::class, "rejectReset"])->name("vm_borrow.reject_reset");

    // VM Service Requests
    Route::post('vm_borrow/{vmBorrow}/service-request', [VmServiceRequestController::class, 'store'])->name('vm_borrow.service_request.store');
    Route::post('vm_borrow/{vmBorrow}/service-request/{serviceRequest}/update', [VmServiceRequestController::class, 'updateStatus'])->name('vm_borrow.service_request.update');

    // VM Host public status (already public above), but admin CRUD below

    // VM Edit Resources — admin only
    Route::post('vm_borrow/{vmBorrow}/edit-resources', [VmBorrowController::class, 'editResources'])->name('vm_borrow.edit_resources');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
| Accessible only by users with role='admin'.
*/

Route::middleware(['auth', 'role:admin'])->group(function () {

    // User Management
    Route::resource('users', UserController::class);

    // Reports — all endpoints admin-only
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

    // VM Reports
    Route::get('reports/vm', [ReportController::class, 'vmAll'])->name('reports.vm.all');
    Route::get('reports/vm/daily', [ReportController::class, 'vmDaily'])->name('reports.vm.daily');
    Route::get('reports/vm/weekly', [ReportController::class, 'vmWeekly'])->name('reports.vm.weekly');
    Route::get('reports/vm/monthly', [ReportController::class, 'vmMonthly'])->name('reports.vm.monthly');
    Route::get('reports/vm/monthly/pdf', [ReportController::class, 'vmMonthlyPdf'])->name('reports.vm.monthly.pdf');

    // VM Host Management (admin only)
    Route::resource('vm_hosts', VmHostController::class)->except(['show']);

    // Inventory CRUD (create, store, destroy) — index/show already in shared group
    Route::get('inventory_items/create', [InventoryItemController::class, 'create'])->name('inventory_items.create');
    Route::post('inventory_items', [InventoryItemController::class, 'store'])->name('inventory_items.store');
    Route::delete('inventory_items/{inventoryItem}', [InventoryItemController::class, 'destroy'])->name('inventory_items.destroy');

    // Bulletin approve/reject — admin only
    Route::post('bulletins/{bulletin}/approve', [BulletinController::class, 'approve'])->name('bulletins.approve');
    Route::post('bulletins/{bulletin}/reject', [BulletinController::class, 'reject'])->name('bulletins.reject');
});

require __DIR__.'/auth.php';
