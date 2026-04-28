<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BulletinController;
use App\Http\Controllers\LabUsageRecordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\LabController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingPageController::class, 'index'])->name('landing');
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

    // Usage record is available for all authenticated users, but scoped per role in controller.
    Route::resource('usage_records', LabUsageRecordController::class)->except(['show', 'edit', 'update']);
});

require __DIR__.'/auth.php';
