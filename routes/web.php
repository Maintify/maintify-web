<?php

use App\Http\Controllers\Auth\WorkshopRegistrationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\Workshop\WorkshopPendingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Maintify Web Routes
|--------------------------------------------------------------------------
*/

// Root redirect
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return redirect()->route('login');
});

// Authenticated & Workshop Approved Routes
Route::middleware(['auth', 'verified', 'workshop.approved'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Vehicle Owner Routes
Route::middleware(['auth', 'verified', 'role:vehicle_owner'])->group(function () {
    Route::resource('vehicles', VehicleController::class);

    // QR Code Management
    Route::get('vehicles/{vehicle}/qr', [QrCodeController::class, 'show'])->name('vehicles.qr.show');
    Route::get('vehicles/{vehicle}/qr/download', [QrCodeController::class, 'download'])->name('vehicles.qr.download');
    Route::post('vehicles/{vehicle}/qr/regenerate', [QrCodeController::class, 'regenerate'])->name('vehicles.qr.regenerate');
});

// Workshop Registration (guest)
Route::get('/register/workshop', [WorkshopRegistrationController::class, 'create'])->name('register.workshop');
Route::post('/register/workshop', [WorkshopRegistrationController::class, 'store']);

// Workshop Pending Approval (authenticated)
Route::get('/workshop/pending', [WorkshopPendingController::class, 'show'])
    ->middleware('auth')
    ->name('workshop.pending');

use App\Http\Controllers\Workshop\ScanController;
use App\Http\Controllers\Workshop\ServiceRecordController;

// Workshop Routes
Route::middleware(['auth', 'verified', 'role:workshop', 'workshop.approved'])->prefix('workshop')->name('workshop.')->group(function () {
    Route::get('/scan', [ScanController::class, 'show'])->name('scan');
    Route::post('/scan/resolve', [ScanController::class, 'resolve'])->name('scan.resolve');

    // Service Records
    Route::get('/service-records/create', [ServiceRecordController::class, 'create'])->name('service-records.create');
    Route::post('/service-records', [ServiceRecordController::class, 'store'])->name('service-records.store');

    Route::get('/reports', function () {
        return view('workshop.reports.index');
    })->name('reports.index');
});

// Super Admin Routes
Route::middleware(['auth', 'verified', 'role:super_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::post('/workshops/{workshop}/approve', [App\Http\Controllers\SuperAdmin\DashboardController::class, 'approve'])->name('workshops.approve');
    Route::post('/workshops/{workshop}/reject', [App\Http\Controllers\SuperAdmin\DashboardController::class, 'reject'])->name('workshops.reject');
});

require __DIR__.'/auth.php';
