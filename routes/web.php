<?php

use App\Http\Controllers\Auth\WorkshopRegistrationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OwnershipTransferController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\ServiceHistoryController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\Workshop\WorkshopPendingController;
use App\Http\Controllers\WorkshopSearchController;
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
    Route::patch('/profile/notifications', [ProfileController::class, 'updateNotifications'])->name('profile.notifications.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
});

// Vehicle Owner Routes
Route::middleware(['auth', 'verified', 'role:vehicle_owner'])->group(function () {
    Route::resource('vehicles', VehicleController::class);
    Route::get('vehicles/{vehicle}/service-history', [ServiceHistoryController::class, 'index'])->name('vehicles.service-history');
    Route::get('workshops/nearby', [WorkshopSearchController::class, 'index'])->name('workshops.nearby');
    Route::get('api/workshops/nearby', [WorkshopSearchController::class, 'search'])->name('api.workshops.nearby');

    // QR Code Management
    Route::get('vehicles/{vehicle}/qr', [QrCodeController::class, 'show'])->name('vehicles.qr.show');
    Route::get('vehicles/{vehicle}/qr/download', [QrCodeController::class, 'download'])->name('vehicles.qr.download');
    Route::post('vehicles/{vehicle}/qr/regenerate', [QrCodeController::class, 'regenerate'])->name('vehicles.qr.regenerate');

    // Ownership Transfer
    Route::get('vehicles/{vehicle}/transfer', [OwnershipTransferController::class, 'create'])->name('vehicles.transfer.create');
    Route::post('vehicles/{vehicle}/transfer', [OwnershipTransferController::class, 'store'])->name('vehicles.transfer.store');

    Route::post('transfers/{transfer}/approve', [OwnershipTransferController::class, 'approve'])->name('transfers.approve');
    Route::post('transfers/{transfer}/reject', [OwnershipTransferController::class, 'reject'])->name('transfers.reject');
    Route::get('transfers/{transfer}/review', [OwnershipTransferController::class, 'review'])->name('transfers.review');
    Route::post('transfers/{transfer}/confirm', [OwnershipTransferController::class, 'confirm'])->name('transfers.confirm');
    Route::get('transfers/{transfer}/success', [OwnershipTransferController::class, 'success'])->name('transfers.success');
});

// Workshop Registration (guest)
Route::get('/register/workshop', [WorkshopRegistrationController::class, 'create'])->name('register.workshop');
Route::post('/register/workshop', [WorkshopRegistrationController::class, 'store']);

// Workshop Pending Approval (authenticated)
Route::get('/workshop/pending', [WorkshopPendingController::class, 'show'])
    ->middleware('auth')
    ->name('workshop.pending');

use App\Http\Controllers\SuperAdmin\AuditLogController;
use App\Http\Controllers\SuperAdmin\SettingController;
use App\Http\Controllers\SuperAdmin\UserController;
use App\Http\Controllers\SuperAdmin\WorkshopController;
use App\Http\Controllers\SuperAdmin\WorkshopVerificationController;
use App\Http\Controllers\Workshop\CustomerController;
use App\Http\Controllers\Workshop\ProfileController as WorkshopProfileController;
use App\Http\Controllers\Workshop\ReportController;
use App\Http\Controllers\Workshop\ScanController;
use App\Http\Controllers\Workshop\ServiceRecordController;
use App\Http\Controllers\Workshop\SparepartController;
use App\Http\Controllers\Workshop\StaffController;

// Workshop Routes
Route::middleware(['auth', 'verified', 'role:workshop', 'workshop.approved'])->prefix('workshop')->name('workshop.')->group(function () {
    Route::get('/scan', [ScanController::class, 'show'])->name('scan');
    Route::post('/scan/resolve', [ScanController::class, 'resolve'])->name('scan.resolve');

    // Spareparts
    Route::resource('spareparts', SparepartController::class)->except(['show']);

    // Staff Management
    Route::resource('staff', StaffController::class)->except(['show']);

    // Customers
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');

    // Workshop Profile Edit
    Route::get('/profile-bengkel', [WorkshopProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile-bengkel', [WorkshopProfileController::class, 'update'])->name('profile.update');

    // Service Records
    Route::get('/service-records', [ServiceRecordController::class, 'index'])->name('service-records.index');
    Route::get('/service-records/create', [ServiceRecordController::class, 'create'])->name('service-records.create');
    Route::post('/service-records', [ServiceRecordController::class, 'store'])->name('service-records.store');
    Route::get('/service-records/{service_record}/edit', [ServiceRecordController::class, 'edit'])->name('service-records.edit');
    Route::put('/service-records/{service_record}', [ServiceRecordController::class, 'update'])->name('service-records.update');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
});

// Super Admin Routes
Route::middleware(['auth', 'verified', 'role:super_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/workshops/pending', [WorkshopVerificationController::class, 'index'])->name('workshops.pending');
    Route::get('/workshops/{workshop}/review', [WorkshopVerificationController::class, 'show'])->name('workshops.review');
    Route::post('/workshops/{workshop}/approve', [WorkshopVerificationController::class, 'approve'])->name('workshops.approve');
    Route::post('/workshops/{workshop}/reject', [WorkshopVerificationController::class, 'reject'])->name('workshops.reject');
    Route::post('/workshops/{workshop}/revision', [WorkshopVerificationController::class, 'requestRevision'])->name('workshops.revision');

    // User Management
    Route::resource('users', UserController::class)->only(['index', 'show', 'update']);

    // Vehicle Monitoring
    Route::resource('vehicles', App\Http\Controllers\SuperAdmin\VehicleController::class)->only(['index', 'show']);

    // Workshop Management
    Route::resource('workshops', WorkshopController::class)->only(['index', 'show', 'update']);

    // Audit Logs
    Route::resource('audit-logs', AuditLogController::class)->only(['index', 'show']);

    // Global Settings
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
});

require __DIR__.'/auth.php';

// API Documentation (Swagger UI)
Route::get('/api/documentation', function () {
    return view('swagger');
});
